<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';

global $certificate, $param, $current_contest;
db_connect (config_get ('check-database'));
$c = certificate_get_by_id($certificate);
if ($c==false)
    return;
$template = $c['template'];
$for = $c['for'];
$limit_id = $c['limit_id'];
$limit = limit_get_by_id($limit_id);
            
$select = 'SELECT DISTINCT ';
if ($current_contest != -1 && $current_contest!='')
{
    $from = 'FROM `team`, ';
    $where = 'WHERE `team`.`contest_id`='.$current_contest.' AND ';
    $table_team_id = db_field_value('visible_table', 'id', "`table`='team'");
    $tables = array($table_team_id);
}
else
{
    $from = 'FROM ';
    $where = ($limit['limit']==''?'':'WHERE ');
    $tables = array();
}
$fields = array();

//echo('<div>test1</div>');
  
preg_match_all("/#([^#]+)#/", $for, $matchesarray, PREG_SET_ORDER);
foreach ($matchesarray as $value) {
    $field = visible_field_get_by_caption($value[1]);
    $table = visible_table_get_by_id($field['table_id']);
    if (!inarr($tables, $table['id']))
    {
        $tables[count($tables)]=$table['id'];
        $from .= $table['table'].', ';
    }
}

//echo('<div>test2</div>');

preg_match_all("/#([^#]+)#/", $template, $matchesarray, PREG_SET_ORDER);
foreach ($matchesarray as $value) {
    $field = visible_field_get_by_caption($value[1]);
    $table = visible_table_get_by_id($field['table_id']);
    if (!inarr($tables, $table['id']))
    {
        $tables[count($tables)]=$table['id'];
        $from .= $table['table'].', ';
    }
    if (!inarr($fields, $value[1]))
    {
        $fields[count($fields)]=$value[1];
        $select .= '`'.$table['table'].'`.`'.$field['field'].'` as '.db_string($value[1]).', ';
    }
}

//echo('<div>test3</div>');
          
preg_match_all('/(\d+) (<|<=|=|>|>=|<>|is null|is not null) (\S*) (OR|AND)/', $limit['limit'], $limits, PREG_SET_ORDER);
$i=0;
foreach ($limits as $value) {
    $field_id = $value[1];
    $operation = $value[2];
    $val = $value[3];
    $connection = $value[4];
        
    $field = visible_field_get_by_id($field_id);
    $table = visible_table_get_by_id($field['table_id']);
    if (!inarr($tables, $table['id']))
    {
        $tables[count($tables)]=$table['id'];
        $from .= $table['table'].', ';
    }
        
    $where .= '`'.$table['table'].'`.`'.$field['field'].'`'.$operation.db_string($val).' '.$connection.' ';
}
      
if ($where != '')
{
    $where = substr($where, 0, strlen($where)-4);
    $where .= ' AND ';
}
      
//echo('<div>test4</div>');

$have_new=true;
while ($have_new)
{
    $have_new=false;
    $n = count($tables);
    for ($i=0; $i<$n; $i++)
        for ($j=$i+1; $j<$n; $j++)
        {
            $connection = db_row_value('table_connections', "`table1_id`=".$tables[$i]." AND `table2_id`=".$tables[$j]);
            if (!$connection)
                $connection = db_row_value('table_connections', "`table1_id`=".$tables[$j]." AND `table2_id`=".$tables[$i]);
            if ($connection!=false)
            {
                $connect = $connection['connection'];
                if ($connect=='')
                {
                    $table = db_row_value("visible_table", "`id`=".$connection['connect_table_id']);
                    if (!inarr($tables, $table['id']))
                    {
                        $have_new = true;
                        $tables[count($tables)]=$table['id'];
                        $from .= $table['table'].', ';
                    }
                }
            }
        }
}
$n = count($tables);
for ($i=0; $i<$n; $i++)
    for ($j=$i+1; $j<$n; $j++)
        {
            $connection = db_row_value('table_connections', "`table1_id`=".$tables[$i]." AND `table2_id`=".$tables[$j]);
            if (!$connection)
                $connection = db_row_value('table_connections', "`table1_id`=".$tables[$j]." AND `table2_id`=".$tables[$i]);
            $connect = $connection['connection'];
            if ($connect != '')
                $where .= $connect.' AND ';
        }
      
$select = substr($select, 0, strlen($select) - 2);
$from = substr($from, 0, strlen($from)-2);

//echo('<div>test5</div>');

$where .= $param;
$sql = $select.' '.$from.' '.$where;
//echo('<div>'.$sql.'</div>');

$result = db_query($sql);
$t = db_row_array($result);

//TODO: It's hack. Need to find normal solution for printing details with separator between them.
if ($t['ФИО ученика'] != "" || $t['ФИО учителя'] != "")
{
    while ($row = db_row_array($result))
    {
        if ($row['ФИО ученика'] != "" && strpos($t['ФИО ученика'], $row['ФИО ученика'])===false){
            $t['ФИО ученика'] .= '<br/>'.$row['ФИО ученика'];
        }
        if ($row['ФИО учителя'] != "" && strpos($t['ФИО учителя'], $row['ФИО учителя'])===false){
            $t['ФИО учителя'] .= ', '.$row['ФИО учителя'];
        }
    }
}

if ($t==false)
    return;

if ($t['Место в параллели']==1)
    $t['Место в параллели'] = I;
else if ($t['Место в параллели']==2)
    $t['Место в параллели'] = II;
else if ($t['Место в параллели']==3)
    $t['Место в параллели'] = III;

//echo('<div>test6</div>');

$matchearray = array();
preg_match_all("/#[^#]+#/", $template, $matchearray);
$n = count($matchearray[0]);
$i=0;
while ($i<$n)
{
    $match = substr($matchearray[0][$i], 1, count($matchearray[0][$i])-2);
    $template = str_replace($matchearray[0][$i], $t[$match], $template);
    $i++;
}    

include("../../../../../lib/MPDF54/mpdf.php");
$mpdf=new mPDF();
$mpdf->WriteHTML(eval_code('<?php global $DOCUMENT_ROOT; ?>'.$template));
$mpdf->Output($c['name'].'.pdf','D');
?>