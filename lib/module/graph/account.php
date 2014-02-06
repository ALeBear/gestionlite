<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>

<?php $colsNumber = 5; ?>

<form action="<?php echo $formaction; ?>" method="post">
<table border="0" cellpadding="3" cellspacing="0">
<tr>
<?php
  $count = 0;
  foreach ($accounts as $anAccount)
  {
    if ($anAccount->isBlackhole())
    {
      continue;
    }
    
    if ($count && $count % $colsNumber == 0)
    {
      echo '</tr><tr>';
    }
    $checked = in_array($anAccount->getId(), $graphAccounts) ? ' checked="checked"' : '';
    echo '<td><input type="checkbox" name="graphAccounts[]" value="' . $anAccount->getId() . '"' . $checked . ' />'
      . $anAccount->getName() . '</td>';
    $count++;
  }
  while ($count % $colsNumber)
  {
    echo '</td>';
    $count++;
  }
?>
</tr>
<tr><td colspan="<?php echo $colsNumber; ?>">
  Rapport : 
  <input type="radio" name="report" value="expenses"<?php if (!$report || $report == 'expenses') echo ' checked="checked"' ?> />Dépenses
  <input type="radio" name="report" value="credits"<?php if ($report == 'credits') echo ' checked="checked"' ?> />Crédits
  <input type="radio" name="report" value="expenses-internals"<?php if ($report == 'expenses-internals') echo ' checked="checked"' ?> />Dépenses internes
  <input type="radio" name="report" value="credits-internals"<?php if ($report == 'credits-internals') echo ' checked="checked"' ?> />Crédits internes
</td></tr>
<tr><td colspan="<?php echo $colsNumber; ?>">
  Type : 
  <input type="radio" name="graphType" value="<?php echo glGraph::TYPE_BAR; ?>"<?php if (!$graphType || $graphType == glGraph::TYPE_BAR) echo ' checked="checked"' ?> />Barres
  <input type="radio" name="graphType"  value="<?php echo glGraph::TYPE_LINE; ?>"<?php if ($graphType == glGraph::TYPE_LINE) echo ' checked="checked"' ?> />Lignes
  &nbsp;
  <input type="checkbox" name="showValues" value="1"<?php if ($showValues) echo ' checked="checked"' ?> /> Avec valeurs
  <input type="submit" value="Afficher" />
</td></tr>
</table>
</form>
<br />

<?php if (isset($graph)): ?>
<img src="<?php echo $graph; ?>" />
<?php endif; ?>
