<?php

?><!DOCTYPE html>
<html>
    <head>
        <title>Entypo+ iconset</title>
        <script src="/ld/js/jquery.js"></script>
        <style>
            .icon16 {width: 16px; border: 1px solid #aaa; }
            .icon24 {width: 24px; border: 1px solid #aaa; }
            .icon32 {width: 32px; border: 1px solid #aaa; }
        </style>
    </head>
    <body>
        <div style="margin-bottom: 10px;">
            <input type="text" id="filter" style="width: 200px;" onkeyup="Refilter('#filter', '.filterable')" />
        </div>
        <div class="iconlist">
            <?php
            $il = glob('*.svg');
            sort($il);
            foreach($il as $icon) {
                ?>
                <div class="filterable" style="display: inline-block; width: 160px; height: 80px; vertical-align: top; text-align: center;">
                    <div><img class="icon32" style="color: red;" src="<?=$icon?>" /></div>
                    <div style="font-family: Tahoma; font-size: 8pt;"><?=str_replace('.svg', '', $icon)?></div>
                </div>
                <?php
            }
            ?>
        </div>
    </body>
</html>
<script>
var ct = null;

$('#filter').focus().select();

function eRefilter(input, target){
	if(ct!=null)clearTimeout(ct);
	ct = setTimeout(function(){Refilter(input, target);}, 300);
}

function Refilter(input, target) {
	var f = $(input).val();
	var selector = target;
	
	if(f.indexOf(' ')!=-1) {
		//фильтр содержит пробелы - учитывать наличие всех слов
		var s = f.split(' ');
		var cond = '';
		for(var i in s) {
			var o = s[i].toLowerCase();
			cond += (cond == '' ? '' : ' && ') + 'elem.innerHTML.toLowerCase().indexOf("'+o.toLowerCase()+'")!=-1'
		}
	
		$(selector).each(function(index, elem){
			if(eval(cond)) {
				elem.style.display = 'inline-block';
			}
			else {
				elem.style.display = 'none';
			}
		});
	}
	else if(f.length == 0) {
		//фильтр пустой - отобразить все строки
		$(selector).each(function(index, elem){
			elem.style.display = 'inline-block';
		});
	}
	else {
		//фильтр не содержит пробелов
		$(selector).each(function(index, elem) {
			if(elem.innerHTML.toLowerCase().indexOf(f.toLowerCase())!=-1) {
				elem.style.display = 'inline-block';
			}
			else {
				elem.style.display = 'none';
			}
		});
	}
}

</script>