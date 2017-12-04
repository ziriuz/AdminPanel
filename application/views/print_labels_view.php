<link rel="stylesheet" href="label.css"/>
  <style type="text/css">
   @media print{
   .newpage{page-break-before: always;}
   } 
  </style>
<table>
<?php 
 $j=0;
 foreach ($labels as $i => $label){
   if($j%3==0) echo"<tr>";
   echo "<td>"; 
   require("templates/".$template.".htm");
   echo "</td>";
   if($j%3==2) echo"</tr>";
   if($j%18==17) echo"</table><div class=\"newpage\"></div><table>";
//   if($j%15==14) echo"</table><div class=\"newpage\"></div><table>";
   $j++;
  }
  if($j%3!=2) echo"</tr>";
?>
</table>