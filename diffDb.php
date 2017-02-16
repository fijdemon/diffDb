<?php
/**
 * Short description.
 *
 * @author  zhaoy QQ:85740885
 * @version 1.0
 * @package main
 */
	$db1 = ['ip' => '127.0.0.1','port' => '3232','user' => 'root','pwd' => 'root','dbname' => 'zy_db1'];

	$db2 = ['ip' => '192.168.1.120','port' => '3306','user' => 'root','pwd' => 'root','dbname' => 'zy_db2'];
	
	//$db2 = ['ip' => '127.0.0.1','port' => '3306','user' => 'root','pwd' => 'root','dbname' => 'sh_teacher'];



	$diff = [
		'db1' => [
			'db'	=> $db1,
			'tables'=> [],
		],
		'db2' => [
			'db'	=> $db2,
			'tables'=> [],
		],
	];
	
	foreach ($diff as $k=>$v){
		$db = new mysqli($v['db']['ip'],$v['db']['user'],$v['db']['pwd'],$v['db']['dbname'],$v['db']['port']);
		$db->query('set names utf8');	
		$rsTable = $db->query("show tables");
		$diff[$k]['tables'] = [];
		while($row = $rsTable->fetch_array())
		{
			$diff[$k]['tables'][$row[0]]['tableName'] = $row[0];
			$rsDesc = $db->query('desc '.$row[0]);
			
			while($rowDesc = $rsDesc->fetch_assoc())
			{
				$diff[$k]['tables'][$row[0]]['desc'][$rowDesc['Field']] = $rowDesc;
			}
			
		}
		
	}
	

	$db1Table = $diff['db1']['tables'];
	$db2Table = $diff['db2']['tables'];

	//对比两个库中相同的表
	$sameTable = array_keys(array_intersect_key ($db1Table,$db2Table));
	
	foreach ($sameTable as $v){

		$sameField = array_keys(array_intersect_key ($db1Table[$v]['desc'],$db2Table[$v]['desc']));
		foreach ($sameField as $vv){
			$sameElement = array_keys(array_intersect ($db1Table[$v]['desc'][$vv],$db2Table[$v]['desc'][$vv]));
			
			foreach ($sameElement as $vvv){
				//删除有相同内容的属性
				/*if($vvv == 'Field')
				{
					continue;
				}*/
				unset($db1Table[$v]['desc'][$vv][$vvv]);
				unset($db2Table[$v]['desc'][$vv][$vvv]);
			}
			//echo "<pre>";var_dump($db1Table[$v]['desc']);echo "</pre>\n";;
			//echo "<pre>";var_dump($db2Table[$v]['desc']);echo "</pre>";die;

			//删除有相同字段的字段
			if(count($db1Table[$v]['desc'][$vv]) == 0)
			{
				unset($db1Table[$v]['desc'][$vv]);
			}
			if(count($db2Table[$v]['desc'][$vv]) == 0)
			{
				unset($db2Table[$v]['desc'][$vv]);
			}
		}

		if(count($db1Table[$v]['desc']) == 0)
		{
			unset($db1Table[$v]);
		}
		if(count($db2Table[$v]['desc']) == 0)
		{
			unset($db2Table[$v]);
		}
	}
	


	//echo "<pre>";var_dump($db1Table);echo "</pre>";
	//echo "<pre>";var_dump($db2Table);echo "</pre>";die;
	$diffTableNames = array_keys(array_flip(array_merge(array_keys($db1Table),array_keys($db2Table))));

//	echo "<pre>";var_dump($db1Table['phpcms_data']['desc']);echo "</pre>";die;
?>
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>
  <style>
	.div1,.div2{float:left;margin-left:100px;width:400px;}
	
  </style>
<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
 </head>
 <body>
 <?php foreach($diffTableNames as $v):?>
	<div class="div1">
	<h3><?=$diff['db1']['db']['ip']?> &nbsp;&nbsp;<?=$v?></h3>

	<table class="table table-hover table-condensed">
		<tbody>
		<tr class="success"><th>字段</th><th>属性</th></tr>
		<?php
			if(is_array($db1Table[$v]['desc']))
			foreach($db1Table[$v]['desc'] as $kk=>$vv):
		?>
		<tr>
		<td><?=$kk;?></td>
		<td>
			<table class="table table-hover table-condensed">
			<?php
				if(is_array($vv))
				foreach($vv as $kkk=>$vvv):
			?>
				<tr>
					<td><?=$kkk;?></td>
					<td><?=$vvv;?></td>
				</tr>
			<?php endforeach;?>
			</table>
		</td>
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	</div>
	<div class="div2">
	<h3><?=$diff['db2']['db']['ip']?> &nbsp;&nbsp;<?=$v?></h3>
	<table class="table table-hover table-condensed">
		<tr class="success"><th>字段</th><th>属性</th></tr>
		<?php
			if(is_array($db2Table[$v]['desc']))
			foreach($db2Table[$v]['desc'] as $kk=>$vv):
		?>
		<tr>
		<td><?=$kk;?></td>
		<td>
			<table class="table table-hover table-condensed">
			<?php
				if(is_array($vv))
				foreach($vv as $kkk=>$vvv):
			?>
				<tr>
					<td><?=$kkk;?></td>
					<td><?=$vvv;?></td>
				</tr>
			<?php endforeach;?>
			</table>
		</td>
		</tr>
		<?php endforeach;?>
	</table>
	</div>
	<div style="clear:both;"></div>
 <?php endforeach;?>
 </body>
</html>
