<?php

final class ArcanistBraceFormattingXHPASTLinterRule
  extends ArcanistXHPASTLinterRule {

  const ID = 24;

  public function getLintName() {
    return pht('Brace Placement');
  }

  public function getLintSeverity() {
    return ArcanistLintSeverity::SEVERITY_WARNING;
  }

  public function process(XHPASTNode $root) {
    foreach ($root->selectDescendantsOfType('n_STATEMENT_LIST') as $list) {
      $tokens = $list->getTokens();
      if (!$tokens || head($tokens)->getValue() != '{') {
        continue;
      }
      list($before, $after) = $list->getSurroundingNonsemanticTokens();

      if (!$before) {
        $first = head($tokens);

        $this->raiseLintAtToken(
          $first,
          pht(
            'Put opening braces on the same line as control statements and '.
            'declarations, with a single space before them.'),
          ' '.$first->getValue());
      } else if (count($before) === 1) {
        $before = reset($before);

        if ($before->getValue() !== ' ') {
          $this->raiseLintAtToken(
            $before,
            pht(
              'Put opening braces on the same line as control statements and '.
              'declarations, with a single space before them.'),
            ' ');
        }
      }
    }

    $nodes = $root->selectDescendantsOfType('n_STATEMENT');
    foreach ($nodes as $node) {
      $parent = $node->getParentNode();

      if (!$parent) {
        continue;
      }

      $type = $parent->getTypeName();
      switch ($type) {
        case 'n_DECLARE':
        case 'n_NAMESPACE':
        case 'n_STATEMENT_LIST':
          break;

        default:
          $this->raiseLintAtNode(
            $node,
            pht('Use braces to surround a statement block.'));
          break;
      }
    }

    $nodes = $root->selectDescendantsOfTypes(array(
      'n_DO_WHILE',
      'n_ELSE',
      'n_ELSEIF',
    ));
    foreach ($nodes as $list) {
      $tokens = $list->getTokens();
      if (!$tokens || last($tokens)->getValue() != '}') {
        continue;
      }
      list($before, $after) = $list->getSurroundingNonsemanticTokens();
      if (!$before) {
        $first = head($tokens);

        $this->raiseLintAtToken(
          $first,
          pht(
            'Put opening braces on the same line as control statements and '.
            'declarations, with a single space before them.'),
          ' '.$first->getValue());
      } else if (count($before) === 1) {
        $before = reset($before);
        if ($before->getValue() !== ' ') {
          $this->raiseLintAtToken(
            $before,
            pht(
              'Put opening braces on the same line as control statements and '.
              'declarations, with a single space before them.'),
            ' ');
        }
      }
    }
  }

}
