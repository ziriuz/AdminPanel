<div class="orderlist">
        <table><tr>
     <td></td>
     <td>Артикул</td>
     <td>Название</td>
     <td>прочие</td>
     <td>40</td>
     <td>42</td>
<td>44</td>
<td>46</td>
<td>48</td>
<td>50</td>
<td>52</td>
<td>54</td>
<td>56</td>
<td>58</td>
<td>-</td>
<td>68</td>
<td>74</td>
<td>80</td>
<td>86</td>
<td>92</td>
<td>98</td>
<td>104</td>
<td>110</td>
<td>116</td>
<td>122</td>
<td>128</td>
<td>134</td>
<td>140</td><td>146</td>
<td>152</td>
     <td>Количество</td>
     <td>Цена</td><td>Цена опт</td><td>Код цвета</td><td>Цвет</td><td>ID</td>
</tr>
 <?php foreach($documentItems as $i=>$docItem):?>
 <tr><td><img src="../prd_lib/images/140/<?=$docItem->foto_name?>" ></td>
     <td><?=$docItem->alt_code?></td>
     <td><?=$docItem->nmcl_name?></td>
     <td><?=$docItem->q_others?></td>
     <td><?=$docItem->q40?></td>
     <td><?=$docItem->q42?></td>
<td><?=$docItem->q44?></td>
<td><?=$docItem->q46?></td>
<td><?=$docItem->q48?></td>
<td><?=$docItem->q50?></td>
<td><?=$docItem->q52?></td>
<td><?=$docItem->q54?></td>
<td><?=$docItem->q56?></td>
<td><?=$docItem->q58?></td>
<td></td>
<td><?=$docItem->q68?></td>
<td><?=$docItem->q74?></td>
<td><?=$docItem->q80?></td>
<td><?=$docItem->q86?></td>
<td><?=$docItem->q92?></td>
<td><?=$docItem->q98?></td>
<td><?=$docItem->q104?></td>
<td><?=$docItem->q110?></td>
<td><?=$docItem->q116?></td>
<td><?=$docItem->q122?></td>
<td><?=$docItem->q128?></td>
<td><?=$docItem->q134?></td>
<td><?=$docItem->q140?></td><td><?=$docItem->q146?></td>
<td><?=$docItem->q152?></td>
     <td><?=$docItem->qty?></td>
     <td><?=$docItem->price?></td>
     <td><?=$docItem->price_min?></td>
     <td><?=$docItem->color_code?></td>
     <td><?=$docItem->color_name?></td>
     <td><?=$docItem->nmcl_id?></td>
 </tr>
 <?php endforeach;?>        
 </table>
</div>