<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

/* @var $rm recurringMovement */
?>
<form action="<?php echo $formaction; ?>" method="post">
<input type="hidden" name="id" value="<?php echo $rm->getId(); ?>" />
<table class="update">
<tr>
  <td class="label">Nom</td>
  <td class="field"><input type="text" name="label" value="<?php echo $rm->getLabel(); ?>" /></td>
</tr>
<tr>
  <td class="label">Montant</td>
  <td class="field"><input type="text" name="amount" value="<?php echo $rm->getAmount(); ?>" /></td>
</tr>
<tr>
  <td class="label">Depuis le compte</td>
  <td class="field">
    <select name="account_from">
      <option value="0"<?php if (!$rm->getAccountFrom()) echo ' selected="selected"'; ?>></option>
    <?php foreach ($accounts as $anAccount): ?>
      <option value="<?php echo $anAccount->getId(); ?>"<?php if ($rm->getAccountFrom() && $anAccount->getId() == $rm->getAccountFrom()->getId()) echo ' selected="selected"'; ?>><?php echo $anAccount->getName(); ?></option>
    <?php endforeach; ?>
    </select>
  </td>
</tr>
<tr>
  <td class="label">Vers le compte</td>
  <td class="field">
    <select name="account_to">
      <option value="0"<?php if (!$rm->getAccountTo()) echo ' selected="selected"'; ?>></option>
    <?php foreach ($accounts as $anAccount): ?>
      <option value="<?php echo $anAccount->getId(); ?>"<?php if ($rm->getAccountTo() && $anAccount->getId() == $rm->getAccountTo()->getId()) echo ' selected="selected"'; ?>><?php echo $anAccount->getName(); ?></option>
    <?php endforeach; ?>
    </select>
  </td>
</tr>
<tr>
  <td class="label">Fréquence</td>
  <td class="field">tou(te)s les <input type="text" name="frequency_every" value="<?php echo $rm->getFrequencyEvery() ? $rm->getFrequencyEvery() : 1; ?>" /></td>
</tr>
<tr>
  <td class="label"></td>
  <td class="field">
    <select name="frequency_type">
      <option value="<?php echo recurringMovement::FREQ_MONTH; ?>"<?php if ($rm->getFrequencyType() == recurringMovement::FREQ_MONTH) echo ' selected="selected"'; ?>>mois</option>
      <option value="<?php echo recurringMovement::FREQ_WEEK; ?>"<?php if ($rm->getFrequencyType() == recurringMovement::FREQ_WEEK) echo ' selected="selected"'; ?>>semaines</option>
    </select>
  </td>
</tr>
<tr>
  <td class="label"></td>
  <td class="field">
    le
    <input type="text" name="frequency_on" value="<?php echo $rm->getFrequencyOn(); ?>" />
    <br />
    (Mettre le jour pour une fréquence mensuelle, ou le numéro du jour pour une fréquence quotidienne :<br />
    Lundi = 1, Mardi = 2, Mercredi = 3, Jeudi = 4, Vendredi = 5, Samedi = 6, Dimanche = 7)
  </td>
</tr>
<tr>
  <td class="label">Jusqu'au</td>
  <td class="field">
    <input type="text" name="dateUntil" value="<?php echo $rm->getUntil() ? date('d/m/Y', strtotime($rm->getUntil())) : ''; ?>" /><br />
    (laisser vide pour sans fin)
  </td>
</tr>
<tr>
  <td align="center" colspan="2"><input type="submit" value="Go" /></td>
</tr>
</table>
</form>
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>recurringMovement">Annuler</a>