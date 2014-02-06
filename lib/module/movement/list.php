<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */
$pm = $nm = $previousMonth = $nextMonth = null;
foreach (array_reverse($months) as $aMonth) {
    if ($nm) {
        $nextMonth = $aMonth;
        $nm = null;
    }
    if ($currentMonth == $aMonth) {
        $previousMonth = $pm;
        $nm = true;
    }
    $pm = $aMonth;
}
?>
<form action="<?php echo DIRECTORY_PREFIX; ?>movement/list" method="get" id="monthsForm">
<?php if ($previousMonth): ?>
<a href="#" onClick="frm = document.getElementById('monthsForm');frm.month.value = '<?php echo $previousMonth; ?>'; frm.submit();return false;">&lt; Préc</a>
<?php endif; ?>
<select name="month" onChange="this.form.submit();" id="month">
<?php foreach ($months as $aMonth): ?>
  <?php $label = dateTranslator::getMonthLabel(substr($aMonth, 5, 2)) . ' ' . substr($aMonth, 0, 4); ?>
  <option value="<?php echo $aMonth; ?>"<?php if ($currentMonth == $aMonth) echo ' selected="selected"'; ?>><?php echo $label; ?></option>
<?php endforeach; ?>
</select>
<?php if ($nextMonth): ?>
<a href="#" onClick="frm = document.getElementById('monthsForm');frm.month.value = '<?php echo $nextMonth; ?>'; frm.submit();return false;">Suiv &gt;</a>
<?php endif; ?>
<a href="<?php echo DIRECTORY_PREFIX; ?>movement/update">Créer un mouvement</a>
</form>
<br />
<table class="list">
<tr>
  <td class="header">Date</td>
  <td class="header">Libellé</td>
  <td class="header">Depuis</td>
  <td class="header">Vers</td>
  <td class="header">Montant</td>
  <td class="header">Actions</td>
</tr>
<?php
$previousDate = false;
foreach ($list as $anItem):
  $datePassed = date('d/m/Y', strtotime($anItem->getDatePassed()));
  $tdclass = $previousDate != $datePassed ? 'bordertop' : 'standard';
  $previousDate = $datePassed;
  switch (true)
  {
    case $anItem->getAccountFrom()->getId() == account::EXTERNAL_ID:
      $amountCss = 'positive';
      break;
    case $anItem->getAccountTo()->getId() == account::EXTERNAL_ID:
      $amountCss = 'negative';
      break;
    default:
      $amountCss = 'neutral';
      break;
  }
?>
<tr>
  <td class="<?php echo $tdclass; ?>"><?php echo ucfirst(dateTranslator::getDayLabel(date('N', strtotime($anItem->getDatePassed())))) . ' ' . date('j', strtotime($anItem->getDatePassed())); ?></td>
  <td class="<?php echo $tdclass; ?> certified <?php echo $anItem->getCertifiedAt() ? 'yes' : 'no'; ?><?php echo $anItem->getFlags() ? ' flagged' : ''; ?>"><?php echo $anItem->getLabel(); ?></td>
  <td class="<?php echo $tdclass; ?>"><?php echo $anItem->getAccountFrom()->getName(); ?></td>
  <td class="<?php echo $tdclass; ?>"><?php echo $anItem->getAccountTo()->getName(); ?></td>
  <td class="amount <?php echo $tdclass . ' ' . $amountCss; ?>"><?php echo $anItem->getAmount(); ?></td>
  <td class="<?php echo $tdclass; ?>">
    <?php if (!$anItem->getCertifiedAt()): ?>
        <?php if (!$anItem->getFlags()): ?>
        <a href="<?php echo DIRECTORY_PREFIX; ?>movement/flag?id=<?php echo $anItem->getId(); ?>&flags=1">Flagger</a>
        <?php else: ?>
        <a href="<?php echo DIRECTORY_PREFIX; ?>movement/flag?id=<?php echo $anItem->getId(); ?>&flags=0">Déflagger</a>
        <?php endif; ?>
    <?php endif; ?>
    <a href="<?php echo DIRECTORY_PREFIX; ?>movement/update?id=<?php echo $anItem->getId(); ?>">Modifier</a>
    <?php if (!$anItem->getCertifiedAt()): ?>
    <a href="<?php echo DIRECTORY_PREFIX; ?>movement/delete?id=<?php echo $anItem->getId(); ?>">Supprimer</a>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</table>
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>movement/update">Créer un mouvement</a>
<br /><br />
<a href="<?php echo DIRECTORY_PREFIX; ?>movementTemplate/list">Gestion des modèles de mouvements</a>
<br /><br />
<a href="<?php echo DIRECTORY_PREFIX; ?>movement/certify" onClick="return confirm('Voulez-vous vraiment certifier tous les mouvements ?');">Certifier tous les mouvement</a>
<br /><br />
<table border="0" cellpadding="3" cellspacing="3" class="list">
<tr>
  <td class="amount positive">Crédit</td>
  <td class="amount negative">Débit</td>
  <td class="amount neutral">Mouvement interne</td>
  <td class="certified yes">Certifié en banque</td>
</tr>
</table>
