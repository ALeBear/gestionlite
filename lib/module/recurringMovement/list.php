<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>
<table class="list">
<tr>
  <td class="header">Nom</td>
  <td class="header">Depuis</td>
  <td class="header">Vers</td>
  <td class="header">Montant</td>
  <td class="header">Récurrence</td>
  <td class="header">Dernier mouvement</td>
  <td class="header">Prochain mouvement</td>
  <td class="header">Actions</td>
</tr>
<?php foreach ($list as $anItem): ?>
<?php
/* @var $anItem recurringMovement */
switch ($anItem->getFrequencyType())
{
  case recurringMovement::FREQ_MONTH:
    $recurrence = 'Tous les %d mois, le ' . $anItem->getFrequencyOn();
    break;
  case recurringMovement::FREQ_WEEK:
    $recurrence = 'Tous les %d semaine(s), le ' . dateTranslator::getDayLabel($anItem->getFrequencyOn());
    break;
}
$recurrence .= $anItem->getUntil() ? '<br />Jusqu\'au ' . date('d/m/Y', strtotime($anItem->getUntil())) : '';
$recurrence = sprintf($recurrence, $anItem->getFrequencyEvery());
?>
<tr>
  <td><?php echo $anItem->getLabel(); ?></td>
  <td><?php echo $anItem->getAccountFrom()->getName(); ?></td>
  <td><?php echo $anItem->getAccountTo()->getName(); ?></td>
  <td class="amount <?php echo $anItem->getAmount() >= 0 ? 'positive' : 'negative'; ?>"><?php echo $anItem->getAmount(); ?></td>
  <td><?php echo $recurrence; ?></td>
  <td><?php echo $anItem->getLastDate() ? date('d/m/Y', strtotime($anItem->getLastDate())) : ''; ?></td>
  <td><?php echo date('d/m/Y', strtotime($anItem->getNextDate())); ?></td>
  <td>
    <a href="<?php echo DIRECTORY_PREFIX; ?>recurringMovement/delete?id=<?php echo $anItem->getId(); ?>">Supprimer</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>recurringMovement/update">Créer un mouvement récurrent</a>