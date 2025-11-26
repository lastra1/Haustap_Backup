<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';
$pdo = haustap_db_conn();
if (!$pdo) { echo json_encode(['success'=>false,'message'=>'DB unavailable']); exit; }
ensure_services_table($pdo);

$services = [
  // Cleaning (Bungalow)
  ['name'=>'Bungalow - Basic Cleaning','category'=>'Cleaning','price'=>1000,'duration_minutes'=>180,'description'=>'Basic Cleaning – 1 Cleaner'],
  ['name'=>'Bungalow - Standard Cleaning','category'=>'Cleaning','price'=>2000,'duration_minutes'=>240,'description'=>'Standard Cleaning – 2 Cleaners'],
  ['name'=>'Bungalow - Deep Cleaning','category'=>'Cleaning','price'=>3000,'duration_minutes'=>300,'description'=>'Deep Cleaning – 3 Cleaners'],
  // Cleaning (Hotel suite)
  ['name'=>'Hotel Suite - Basic Cleaning','category'=>'Cleaning','price'=>1800,'duration_minutes'=>240,'description'=>'Basic Cleaning – 2 Cleaners'],
  // Cleaning (House Container Single)
  ['name'=>'House Container Single - Standard Cleaning','category'=>'Cleaning','price'=>1200,'duration_minutes'=>180,'description'=>'Standard – 1 Cleaner'],
  ['name'=>'House Container Single - Deep Cleaning','category'=>'Cleaning','price'=>1200,'duration_minutes'=>200,'description'=>'Deep – 1 Cleaner'],
  // Cleaning (Villa Larger)
  ['name'=>'Villa (Larger) - Standard Cleaning','category'=>'Cleaning','price'=>12000,'duration_minutes'=>480,'description'=>'Standard – 5 Cleaners'],
  ['name'=>'Villa (Larger) - Deep Cleaning','category'=>'Cleaning','price'=>20000,'duration_minutes'=>600,'description'=>'Deep – 6 Cleaners'],
  // Outdoor - Gardening (basic, standard)
  ['name'=>'Gardening - Basic (1 gardener)','category'=>'Outdoor','price'=>500,'duration_minutes'=>120,'description'=>'Grass cutting / trimming, cleanup'],
  ['name'=>'Gardening - Standard (2 gardeners)','category'=>'Outdoor','price'=>1000,'duration_minutes'=>180,'description'=>'Expanded tasks with two gardeners'],
  // Outdoor - Gardening Large
  ['name'=>'Gardening (Large) - Basic (1 gardener)','category'=>'Outdoor','price'=>800,'duration_minutes'=>180,'description'=>'Large lot basic service'],
  ['name'=>'Gardening (Large) - Standard (2 gardeners)','category'=>'Outdoor','price'=>1600,'duration_minutes'=>240,'description'=>'Large lot standard service'],
  // Tech & Gadget
  ['name'=>'Gaming Console Repair','category'=>'Tech','price'=>300,'duration_minutes'=>60,'description'=>'Console software update / optimization'],
  ['name'=>'TV Repair (up to 32")','category'=>'Tech','price'=>600,'duration_minutes'=>60,'description'=>'Display/sound troubleshooting'],
  ['name'=>'TV Repair (33" to 50")','category'=>'Tech','price'=>800,'duration_minutes'=>90,'description'=>'Display/sound troubleshooting'],
  ['name'=>'TV Repair (51" to 80")','category'=>'Tech','price'=>1000,'duration_minutes'=>120,'description'=>'Display/sound troubleshooting'],
  // Beauty - Lashes
  ['name'=>'Lash Removal','category'=>'Beauty','price'=>500,'duration_minutes'=>60,'description'=>'Safe removal of extensions'],
  ['name'=>'Lash Retouch / Refill (2–3 weeks)','category'=>'Beauty','price'=>800,'duration_minutes'=>90,'description'=>'Fills in gaps from shedding'],
  // Beauty - Makeup (prices may appear mojibake in HTML; normalized here)
  ['name'=>'Makeup - Natural Day Look','category'=>'Beauty','price'=>700,'duration_minutes'=>90,'description'=>'Light foundation, natural eyes'],
  ['name'=>'Makeup - Evening/Party','category'=>'Beauty','price'=>1200,'duration_minutes'=>120,'description'=>'Full coverage, bold eyes'],
  ['name'=>'Makeup - Bridal (Trial + Wedding Day)','category'=>'Beauty','price'=>5000,'duration_minutes'=>300,'description'=>'Trial plus wedding day look'],
  // Nails
  ['name'=>'Manicure','category'=>'Beauty','price'=>250,'duration_minutes'=>45,'description'=>'Standard manicure service'],
  ['name'=>'Pedicure','category'=>'Beauty','price'=>300,'duration_minutes'=>50,'description'=>'Standard pedicure service'],
  ['name'=>'Gel Manicure','category'=>'Beauty','price'=>700,'duration_minutes'=>60,'description'=>'Gel manicure'],
  ['name'=>'Gel Pedicure','category'=>'Beauty','price'=>800,'duration_minutes'=>70,'description'=>'Gel pedicure'],
  // Home Repairs
  ['name'=>'Electrical Repair - Basic Fix','category'=>'Home Repairs','price'=>800,'duration_minutes'=>90,'description'=>'Outlet/switch repair, minor wiring fix'],
  ['name'=>'Plumbing - Leak Fix','category'=>'Home Repairs','price'=>900,'duration_minutes'=>90,'description'=>'Leak detection and repair for faucets/pipes'],
  ['name'=>'Appliance Repair - Diagnosis','category'=>'Home Repairs','price'=>700,'duration_minutes'=>60,'description'=>'Initial diagnostic and basic repair'],
  ['name'=>'Handyman - Minor Repairs','category'=>'Home Repairs','price'=>600,'duration_minutes'=>60,'description'=>'General minor home repairs'],
  // Wellness
  ['name'=>'Home Massage - 60 mins','category'=>'Wellness','price'=>800,'duration_minutes'=>60,'description'=>'Relaxation massage at home'],
  ['name'=>'Home Massage - 90 mins','category'=>'Wellness','price'=>1100,'duration_minutes'=>90,'description'=>'Extended relaxation massage'],
  ['name'=>'Yoga Session - 60 mins','category'=>'Wellness','price'=>600,'duration_minutes'=>60,'description'=>'Guided yoga session at home'],
  ['name'=>'Spa Facial - Basic','category'=>'Wellness','price'=>900,'duration_minutes'=>75,'description'=>'Cleansing, exfoliation, and mask'],
  // Tech & Gadget (additional)
  ['name'=>'Laptop & Desktop PC - Fan / Cooling Repair (Laptop)','category'=>'Tech','price'=>500,'duration_minutes'=>60,'description'=>'Cooling system repair for laptop'],
  ['name'=>'Laptop & Desktop PC - Keyboard Replacement (Laptop)','category'=>'Tech','price'=>500,'duration_minutes'=>60,'description'=>'Laptop keyboard replacement and testing'],
  ['name'=>'Laptop & Desktop PC - OS Reformat + Software Installation (Laptop)','category'=>'Tech','price'=>700,'duration_minutes'=>90,'description'=>'OS reinstall and basic apps for laptop'],
  ['name'=>'Laptop & Desktop PC - Fan / Cooling Repair (Desktop PC)','category'=>'Tech','price'=>500,'duration_minutes'=>60,'description'=>'Cooling system repair for desktop'],
  ['name'=>'Laptop & Desktop PC - Keyboard Replacement (Desktop PC)','category'=>'Tech','price'=>500,'duration_minutes'=>60,'description'=>'Desktop keyboard replacement and testing'],
  ['name'=>'Laptop & Desktop PC - OS Reformat + Software Installation (Desktop PC)','category'=>'Tech','price'=>700,'duration_minutes'=>90,'description'=>'OS reinstall and apps for desktop'],
  ['name'=>'Mobile Phone - Screen Replacement','category'=>'Tech','price'=>1500,'duration_minutes'=>90,'description'=>'Screen replacement (parts not included)'],
  ['name'=>'Tablet & iPad - OS Reinstall','category'=>'Tech','price'=>800,'duration_minutes'=>90,'description'=>'System reinstall and setup'],
  ['name'=>'Game & Console - System Update','category'=>'Tech','price'=>300,'duration_minutes'=>60,'description'=>'Firmware and system updates'],
  // Outdoor (additional)
  ['name'=>'Outdoor - Tree Trimming','category'=>'Outdoor','price'=>1200,'duration_minutes'=>180,'description'=>'Safe trimming of small to medium trees'],
  ['name'=>'Outdoor - Yard Cleanup','category'=>'Outdoor','price'=>900,'duration_minutes'=>150,'description'=>'Debris removal and general yard cleanup'],
];

$inserted = 0; $updated = 0;
foreach ($services as $s) {
  try {
    $stmt = $pdo->prepare('SELECT id FROM services WHERE name = ?');
    $stmt->execute([$s['name']]);
    $row = $stmt->fetch();
    if ($row) {
      $pdo->prepare('UPDATE services SET category=?, price=?, duration_minutes=?, description=?, updated_at=NOW() WHERE id=?')
          ->execute([$s['category'],$s['price'],$s['duration_minutes'],$s['description'],$row['id']]);
      $updated++;
    } else {
      $pdo->prepare('INSERT INTO services (name,category,price,duration_minutes,description,created_at,updated_at) VALUES (?,?,?,?,?,NOW(),NOW())')
          ->execute([$s['name'],$s['category'],$s['price'],$s['duration_minutes'],$s['description']]);
      $inserted++;
    }
  } catch (Throwable $e) {}
}

echo json_encode(['success'=>true,'inserted'=>$inserted,'updated'=>$updated,'count'=>($inserted+$updated)]);