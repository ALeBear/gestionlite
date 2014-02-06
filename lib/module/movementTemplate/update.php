<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>
<form action="<?php echo $formaction; ?>" method="post">
<input type="hidden" name="id" value="<?php echo $movementTemplate->getId(); ?>" />
<table class="update">
<tr>
  <td class="label">Nom du modèle</td>
  <td class="field"><input type="text" name="label" value="<?php echo $movementTemplate->getLabel(); ?>" /></td>
</tr>
<tr>
  <td class="label">Nom du mouvement</td>
  <td class="field"><input type="text" name="movementLabel" value="<?php echo $movementTemplate->getMovementLabel(); ?>" /></td>
</tr>
<tr>
  <td class="label">Montant par défaut (facultatif)</td>
  <td class="field"><input type="text" name="amount" value="<?php echo $movementTemplate->getAmount(); ?>" /></td>
</tr>
<tr>
  <td class="label">Depuis le compte</td>
  <td class="field">
    <select name="account_from" id="account_from">
      <option value="0"<?php if (!$movementTemplate->getAccountFrom()) echo ' selected="selected"'; ?>></option>
    <?php foreach ($accounts as $anAccount): ?>
      <option value="<?php echo $anAccount->getId(); ?>"<?php if ($movementTemplate->getAccountFrom() && $anAccount->getId() == $movementTemplate->getAccountFrom()->getId()) echo ' selected="selected"'; ?>><?php echo $anAccount->getName(); ?></option>
    <?php endforeach; ?>
    </select>
  </td>
</tr>
<tr>
  <td class="label">Vers le compte</td>
  <td class="field">
    <select name="account_to" id="account_to" onChange="autoAccount(this, document.getElementById('account_from'));">
      <option value="0"<?php if (!$movementTemplate->getAccountTo()) echo ' selected="selected"'; ?>></option>
    <?php foreach ($accounts as $anAccount): ?>
      <option value="<?php echo $anAccount->getId(); ?>"<?php if ($movementTemplate->getAccountTo() && $anAccount->getId() == $movementTemplate->getAccountTo()->getId()) echo ' selected="selected"'; ?>><?php echo $anAccount->getName(); ?></option>
    <?php endforeach; ?>
    </select>
  </td>
</tr>
<tr>
  <td align="center" colspan="2"><input type="submit" value="Go" /></td>
</tr>
</table>
</form>
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>movementTemplate">Annuler</a>