<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>
<table class="list">
<tr>
  <td class="header">Nom du modèle</td>
  <td class="header">Nom du mouvement</td>
  <td class="header">Depuis</td>
  <td class="header">Vers</td>
  <td class="header">Montant</td>
  <td class="header">Actions</td>
</tr>
<?php 
    $totalProv = $totalPmt = 0;
    foreach ($list as $anItem):
         preg_match('/^Prov/', $anItem->getLabel()) && $totalProv += $anItem->getAmount();
         preg_match('/^Pmt/', $anItem->getLabel()) && $totalPmt += $anItem->getAmount();
?>
<tr>
  <td><?php echo $anItem->getLabel(); ?></td>
  <td><?php echo $anItem->getMovementLabel(); ?></td>
  <td><?php echo $anItem->getAccountFrom()->getName(); ?></td>
  <td><?php echo $anItem->getAccountTo()->getName(); ?></td>
  <td><?php echo $anItem->getAmount() ? number_format($anItem->getAmount(), 2) : ''; ?></td>
  <td>
    <a href="<?php echo DIRECTORY_PREFIX; ?>movementTemplate/update?id=<?php echo $anItem->getId(); ?>">Modifier</a>
    <a href="<?php echo DIRECTORY_PREFIX; ?>movementTemplate/delete?id=<?php echo $anItem->getId(); ?>">Supprimer</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
<br />
<?php

if ($totalProv) echo sprintf('<b>Total Prov: $%s</b><br/>', number_format($totalProv, 2));
if ($totalPmt) echo sprintf('<b>Total Pmt: $%s</b><br/>', number_format($totalPmt, 2));

?>
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>movementTemplate/update">Créer un modèle</a>
