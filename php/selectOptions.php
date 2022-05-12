<?php

  echo "<table>
          <tr>";


  switch ($parcours) {
    case 'gsi':
    for ($i=1; $i < 9; $i++) {echo "<td>Choix $i</td>";}
    echo "</tr>
          <tr id='row-choix'>";
      for ($i=0; $i < 8; $i++) {
        echo "<td>
                <select id='c$i' name='choix$i' onchange='updateOption(this)'>
                  <option name='choix' value='default'>Choisir</option>
                  <option name='choix' value='HPDA'>HPDA</option>
                  <option name='choix' value='BI'>BI</option>
                  <option name='choix' value='CS'>CS</option>
                  <option name='choix' value='IAC'>IAC</option>
                  <option name='choix' value='IAP'>IAP</option>
                  <option name='choix' value='ICC'>ICC</option>
                  <option name='choix' value='INEM'>INEM</option>
                  <option name='choix' value='VISUA'>VISUA</option>
                </select>
              </td>";
      }
      break;

    case 'mf':
    for ($i=1; $i < 3; $i++) {echo "<td>Choix $i</td>";}
    echo "</tr>
          <tr>";
      for ($i=0; $i < 2; $i++) {
        echo "<td>
                <select id='c$i' name='choix$i' onchange='updateOption(this)'>
                  <option name='choix' value='default'>Choisir</option>
                  <option name='choix' value='ACTU'>Actu</option>
                  <option name='choix' value='MMF'>MMF</option>
                </select>
              </td>";
      }
      break;

    case 'mi':
    for ($i=1; $i < 7; $i++) {echo "<td>Choix $i</td>";}
    echo "</tr>
          <tr>";
      for ($i=0; $i < 6; $i++) {
        echo "<td>
                <select id='c$i' name='choix$i' onchange='updateOption(this)'>
                  <option name='choix' value='default'>Choisir</option>
                  <option name='choix' value='HPDA'>HPDA</option>
                  <option name='choix' value='BI'>BI</option>
                  <option name='choix' value='DS'>DS</option>
                  <option name='choix' value='FT'>FT</option>
                  <option name='choix' value='IAC'>IAC</option>
                  <option name='choix' value='IAP'>IAP</option>
                </select>
              </td>";
      }
      break;

    default:
      echo "Erreur: Parcours inconnue !";
      break;
  }
  echo "  </tr>
        </table>";
 ?>
