<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';
$pdo = haustap_db_conn();
if (!$pdo) { echo json_encode(['success'=>false,'message'=>'DB unavailable']); exit; }
ensure_services_table($pdo);

function read_file($p){ $c=@file_get_contents($p); return $c===false?'':$c; }
function parse_price($txt){ $t=str_replace(['₱','â‚±',','],['','',''],$txt); $t=preg_replace('/[^0-9]/','',$t); return $t?intval($t):null; }
function upsert($pdo,$name,$category,$price,$desc){ if (!$name) return; try{
  $stmt=$pdo->prepare('SELECT id FROM services WHERE name=?'); $stmt->execute([$name]); $row=$stmt->fetch();
  if ($row){ $pdo->prepare('UPDATE services SET category=?, price=?, description=?, updated_at=NOW() WHERE id=?')->execute([$category,$price,$desc,$row['id']]); return 'updated'; }
  else { $pdo->prepare('INSERT INTO services (name,category,price,description,created_at,updated_at) VALUES (?,?,?,?,NOW(),NOW())')->execute([$name,$category,$price,$desc]); return 'inserted'; }
}catch(Throwable $e){ return 'error'; }}

$root = dirname(__DIR__,2);
$dirs = [
  $root.'/Homecleaning',
  $root.'/Outdoor_Services',
  $root.'/Indoor_services',
  $root.'/beauty_services',
  $root.'/wellness_services',
  $root.'/tech_gadget'
];

$results = [];
foreach ($dirs as $dir){ if (!is_dir($dir)) continue; $files = glob($dir.'/*.php'); $category = basename($dir);
  foreach ($files as $f){ $html = read_file($f); if (!$html) continue; $items=[];
    // Cleaning cards
    if (preg_match_all('/<div class="cleaning-title">([^<]+)<\/div>.*?<div class="cleaning-price">([^<]+)<\/div>/si',$html,$m,PREG_SET_ORDER)){
      foreach($m as $match){ $name=trim(html_entity_decode($match[1])); $price=parse_price($match[2]); $items[]=['name'=>$name,'price'=>$price]; }
    }
    // Generic service title/price
    if (preg_match_all('/<h3 class="service-title">([^<]+)<\/h3>.*?<p class="service-price">([^<]+)<\/p>/si',$html,$m2,PREG_SET_ORDER)){
      foreach($m2 as $match){ $name=trim(html_entity_decode($match[1])); $price=parse_price($match[2]); $items[]=['name'=>$name,'price'=>$price]; }
    }
    if (preg_match_all('/<div class="service-title">([^<]+)<\/div>.*?<div class="service-price">([^<]+)<\/div>/si',$html,$m3,PREG_SET_ORDER)){
      foreach($m3 as $match){ $name=trim(html_entity_decode($match[1])); $price=parse_price($match[2]); $items[]=['name'=>$name,'price'=>$price]; }
    }
    // Beauty makeup cards
    if (preg_match_all('/<h3>([^<]+)<\/h3>\s*<p class="price">([^<]+)<\/p>/si',$html,$m4,PREG_SET_ORDER)){
      foreach($m4 as $match){ $name=trim(html_entity_decode($match[1])); $price=parse_price($match[2]); $items[]=['name'=>$name,'price'=>$price]; }
    }
    // Fallback: any element with class price and nearest preceding title
    if (empty($items) && preg_match_all('/class="[^"]*price[^"]*"[^>]*>([^<]+)<\/[^>]+>/si',$html,$mp,PREG_SET_ORDER)){
      foreach($mp as $pm){ $price=parse_price($pm[1]); $name=null; if (preg_match('/(service-title|cleaning-title)">([^<]+)</si',$html,$mt)) $name=trim(html_entity_decode($mt[2])); if ($name && $price){ $items[]=['name'=>$name,'price'=>$price]; }}
    }
    foreach($items as $it){ $desc='Imported from UI ('.$category.')'; $state=upsert($pdo,$it['name'],$category,$it['price'],$desc); $results[]=['file'=>basename($f),'name'=>$it['name'],'price'=>$it['price'],'state'=>$state]; }
  }
}

echo json_encode(['success'=>true,'count'=>count($results),'items'=>$results]);