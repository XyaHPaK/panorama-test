<?php

$mysqli = new mysqli("localhost", "wistra", "123789", "wistra");
$article = 'A124';
$query = $mysqli->query("SELECT g.name,
MAX(case WHEN af.name = 'Color' THEN af.name END) as field_name_1,
MAX(case WHEN af.name = 'Color' THEN av.name END) as field_value_1,
MAX(case WHEN af.name = 'Size' THEN af.name END) as field_name_2,
MAX(case WHEN af.name = 'Size' THEN av.name END) as field_value_2
FROM goods g 
JOIN additional_goods_field_values ag on ag.good_id = g.id
JOIN additional_field_values av on ag.additional_field_value_id = av.id
JOIN additional_fields af on av.additional_field_id = af.id 
WHERE g.article = '$article' GROUP BY g.id;");

echo '<pre>'; var_dump($query->fetch_object()); echo '</pre>';