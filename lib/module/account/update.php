<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>
<form action="<?php echo $formaction; ?>" method="post">
<input type="hidden" name="id" value="<?php echo $account->getId(); ?>" />
<table class="update">
<tr>
  <td class="label">Nom</td>
  <td class="field"><input type="text" name="name" value="<?php echo $account->getName(); ?>" /></td>
</tr>
<tr>
  <td align="center" colspan="2"><input type="submit" value="Go" /></td>
</tr>
</table>
</form>
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>account">Annuler</a>