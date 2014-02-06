<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

/* @var $movement movement */
?>
<script type="text/javascript">
function lastDayOfMonth(month) {
  if (month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) {
    return 31;
  }
  if (month == 4 || month == 6 || month == 9 || month == 11) {
    return 30;
  }
  
  //February...
  var now = new Date();
  var year = now.getFullYear(); 
  return year % 4  && (!(year %100) || year % 400) ? 28 : 29; 
}
  
function addToDate(dateFieldId, change) {
  var dateField = document.getElementById(dateFieldId)
  var date = dateField.value;
  if (date == 'undefined' || date.length != 10) {
    return null;
  }
  
  var day = date.substr(0, 2) - 0;
  var month = date.substr(3, 2) - 0;
  var year = date.substr(6, 4) - 0;
  
  var newDay = day + change;
  var newMonth = month;
  var newYear = year;
  
  if (newDay < 1) {
    newMonth -= 1;
    if (newMonth < 1) {
      newMonth = 12;
      newYear -= 1;
    }
    newDay = lastDayOfMonth(newMonth);
  }
  
  if (newDay > lastDayOfMonth(newMonth)) {
    newDay = 1;
    newMonth += 1;
    if (newMonth > 12) {
      newYear += 1;
      newMonth = 1;
    }
  }
  
  dateField.value = newDay < 10 ? '0' + newDay + '/' : newDay + '/';
  dateField.value += newMonth < 10 ? '0' + newMonth + '/' : newMonth + '/';
  dateField.value += newYear;
}
  
//Update the other account drop-down after changing the first one so it
//selects the external account
function autoAccount(changedDD, otherDD)
{
  var changedValue = changedDD.options[changedDD.selectedIndex].value;
  var otherValue = otherDD.options[otherDD.selectedIndex].value;
  var externalAccountID = <?php echo $externalAccountID; ?>;
  
  if (changedValue && otherValue == 0)
  {
    //Find the index of the external account and set it as selected
    for (var i = 0; i < otherDD.length; i++)
    {
      anOption = otherDD.options[i];
      if (anOption.value == externalAccountID)
      {
        anOption.selected = true;
        return;
      }
    }
  }
}
templates = {};
<?php 
  foreach ($templates as $aTemplate) {
    /* @var $aTemplate movementTemplate */
    echo sprintf("templates[%s] = { accountFrom: %s, accountTo: %s, amount: %s, label: '%s' };\n",
      $aTemplate->getId(),
      $aTemplate->getAccountFrom()->getId(),
      $aTemplate->getAccountTo()->getId(),
      $aTemplate->getAmount() ? $aTemplate->getAmount() : 0,
      str_replace("'", "\'", $aTemplate->getMovementLabel()));
  }
?>

function applyTemplate(id)
{
  if (!id) {
    return;
  }
  document.getElementById('label').value = templates[id].label;
  document.getElementById('account_from').value = templates[id].accountFrom;
  document.getElementById('account_to').value = templates[id].accountTo;
  if (templates[id].amount) {
    document.getElementById('amountQuick').value = templates[id].amount;
  }
}

function getFormModeLinkLabel(mode)
{
  return mode == 'quick' ? 'Formulaire complet' : 'Formulaire rapide';
}

//Chenges the forme from quick mode to complete and vice-versa
function toggleFormMode()
{
  movementForm = document.getElementById('movementForm');
  newMode = movementForm.getAttribute('mode') == 'quick' ? 'complete' : 'quick';
  
  document.getElementById('formModeLink').innerHTML = getFormModeLinkLabel(newMode);
  document.getElementById('form_quick').style.display = newMode == 'quick' ? 'block' : 'none';
  document.getElementById('form_complete').style.display = newMode == 'quick' ? 'none' : 'block';
  document.getElementById('formMode').value = newMode;
  
  movementForm.setAttribute('mode', newMode);
}
</script>
<?php if ($movement->getCertifiedAt()): ?>
<strong>Ce mouvement est certifié</strong>
<?php endif;?>
<form action="<?php echo $formaction; ?>" method="post" id="movementForm" mode="<?php echo $form_mode; ?>">
<input type="hidden" name="id" value="<?php echo $movement->getId(); ?>" />
<input type="hidden" id="formMode" name="formMode" value="<?php echo $form_mode; ?>" />
  
<div id="form_quick" style="display: <?php echo $form_mode == 'quick' ? 'block' : 'none'; ?>">
  <table class="update">
  <tr>
    <td class="label">Modèle</td>
    <td class="field">
      <select name="template" id="template" onChange="applyTemplate(this.options[this.selectedIndex].value);">
        <option value="0" <?php if (!$template) echo ' selected="selected"'; ?>></option>
      <?php foreach ($templates as $aTemplate): ?>
        <option value="<?php echo $aTemplate->getId(); ?>"<?php if ($template == $aTemplate->getId()) echo ' selected="selected"'; ?>><?php echo $aTemplate->getLabel(); ?></option>
      <?php endforeach; ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="label">Date</td>
    <td class="field"><input type="button" value="-" onClick="addToDate('datePassedQuick', -1);" /><input type="text" id="datePassedQuick" name="datePassedQuick" value="<?php echo $movement->getDatePassed() ? date('d/m/Y', strtotime($movement->getDatePassed())) : date('d/m/Y'); ?>" style="width: 100px" /><input type="button" value="+" onClick="addToDate('datePassedQuick', 1);" /></td>
  </tr>
  <tr>
    <td class="label">Montant</td>
    <td class="field">
      <?php if ($movement->getCertifiedAt()): ?>
      <?php echo $movement->getAmount(); ?>
      <?php else: ?>
      <input type="text" id="amountQuick" name="amountQuick" value="<?php echo $movement->getAmount(); ?>" />
      <?php endif;?>
    </td>
  </tr>
  </table>
</div> 
  
<div id="form_complete" style="display: <?php echo $form_mode == 'quick' ? 'none' : 'block'; ?>">
  <table class="update">
  <tr>
    <td class="label">Libellé</td>
    <td class="field"><input type="text" id="label" name="label" value="<?php echo $movement->getLabel(); ?>" /></td>
  </tr>
  <tr>
    <td class="label">Date</td>
    <td class="field"><input type="button" value="-" onClick="addToDate('datePassed', -1);" /><input type="text" id="datePassed" name="datePassed" value="<?php echo $movement->getDatePassed() ? date('d/m/Y', strtotime($movement->getDatePassed())) : date('d/m/Y'); ?>" style="width: 100px" /><input type="button" value="+" onClick="addToDate('datePassed', 1);" /></td>
  </tr>
  <tr>
    <td class="label">Montant</td>
    <td class="field">
      <?php if ($movement->getCertifiedAt()): ?>
      <?php echo $movement->getAmount(); ?>
      <?php else: ?>
      <input type="text" name="amount" value="<?php echo $movement->getAmount(); ?>" />
      <?php endif;?>
    </td>
  </tr>
  <tr>
    <td class="label">Depuis le compte</td>
    <td class="field">
      <?php if ($movement->getId()): ?>
      <?php echo $movement->getAccountFrom()->getName(); ?>
      <?php else: ?>
      <select name="account_from" id="account_from" onChange="autoAccount(this, document.getElementById('account_to'));">
        <option value="0"<?php if (!$movement->getAccountFrom()) echo ' selected="selected"'; ?>></option>
      <?php foreach ($accounts as $anAccount): ?>
        <option value="<?php echo $anAccount->getId(); ?>"<?php if ($movement->getAccountFrom() && $anAccount->getId() == $movement->getAccountFrom()->getId()) echo ' selected="selected"'; ?>><?php echo $anAccount->getName(); ?></option>
      <?php endforeach; ?>
      <?php endif; ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="label">Vers le compte</td>
    <td class="field">
      <?php if ($movement->getId()): ?>
      <?php echo $movement->getAccountTo()->getName(); ?>
      <?php else: ?>
      <select name="account_to" id="account_to" onChange="autoAccount(this, document.getElementById('account_from'));">
        <option value="0"<?php if (!$movement->getAccountTo()) echo ' selected="selected"'; ?>></option>
      <?php foreach ($accounts as $anAccount): ?>
        <option value="<?php echo $anAccount->getId(); ?>"<?php if ($movement->getAccountTo() && $anAccount->getId() == $movement->getAccountTo()->getId()) echo ' selected="selected"'; ?>><?php echo $anAccount->getName(); ?></option>
      <?php endforeach; ?>
      <?php endif; ?>
      </select>
    </td>
  </tr>
  </table>
</div>
<div style="padding-left: 90px;"><input type="submit" value="Go" /></div>
</form>
<br />
<?php if ($allow_quick_form): ?>
<a href="#" onClick="toggleFormMode();" id="formModeLink"><script type="text/javascript">document.write(getFormModeLinkLabel('<?php echo $form_mode; ?>'));</script></a>
<?php endif; ?>
<a href="<?php echo DIRECTORY_PREFIX; ?>movement">Annuler</a>