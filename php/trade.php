<?php
if(!isset($nojs) || !$nojs) require('funcwm.php');

if(!$is_connected)
    exit('<div id=notcon>Vous devez être connecté pour accéder au contenu de cette page</div>');
?>
<div id=ctn-trade>


<h1><i class="fa fa-building"></i>  Bureau des échanges</h1>

<h4><i class="fa fa-exchange"></i> Consultez la <a class="faq white" href="/faq">FAQ</a> pour la description des services ci-dessous</h4>

	<table>
		<tr>
			<td><div id="ba27" class="groupe-4" data-gr="4" data-title="<span class=substy>Premier contact</span><br>Tout le monde y passe.<br><i class=listl>niveau 5</i>"><i class="fa fa-male"></i> Approuvé</div></td>
			<td><div id="ba11" class="groupe-8" data-gr="8" data-title="<span class=substy>Café poster</span><br>En permanence dans le café<br><i class=listl>niveau 4</i>"><i class="fa fa-weixin"></i> Citoyen</div></td>
			<td><i class="fa fa-recycle"></i> Nouveau pseudo</td>
		</tr>

		<tr>
			<td><i class="fa fa-trophy"></i> 5 points</td>
			<td><i class="fa fa-trophy"></i> 10 points</td>
			<td><i class="fa fa-trophy"></i> Prochainement</td>
		</tr>

		<tr>
			<td class="send-trade ck">Echanger</td>
			<td class="send-trade cy">Echanger</td>
			<td class=send-trade>Echanger</td>
		</tr>

		<tr class=td-cc>
			<td>
    			<div class="hide ctn-ck">
    			<input type="text" name="trade-ck" class=trade-ck placeholder="Pseudo...">
    			<input type="submit" name="sbm-ck" class=sbm-ck value="Confirmer">
    			</div>
			</td>

			<td>
    			<div class="hide ctn-cy">
    			<p class=trade-cy>Assigner le badge Citoyen à votre compte ?</p>
    			<input type="submit" name="sbm-cy" class=sbm-cy value="Confirmer">
    			</div>
			</td>
		</tr>
	</table>

</div>