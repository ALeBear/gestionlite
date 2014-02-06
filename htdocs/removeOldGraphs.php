<?php 
/**
 * Remove old graphs images
 * 
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module htdocs
 */

foreach (glob(GRAPHS_DIRECTORY_ABSOLUTE . '/*') as $aGraphFile)
{
  //Remove files older than 12 hours
  if (time() - filemtime($aGraphFile) > 60 * 60 * 12)
  {
     unlink($aGraphFile);
  }
}

?>