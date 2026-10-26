<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__.'/../config/database.php';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function url($p=''){return BASE_URL.'/'.ltrim($p,'/');}
function user(){return $_SESSION['user']??null;}
function require_login(){if(!user()){header('Location: '.url('auth/login.php'));exit;}}
function require_admin(){require_login();if(user()['role']!=='admin'){http_response_code(403);die('Acceso denegado');}}
function csrf(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(24));return $_SESSION['csrf'];}
function check_csrf(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??''))die('Solicitud inválida');}
function flash($m,$t='ok'){$_SESSION['flash']=[$m,$t];}
function take_flash(){ $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function timeago($d){$s=time()-strtotime($d); if($s<60)return 'hace instantes';if($s<3600)return 'hace '.floor($s/60).' min';if($s<86400)return 'hace '.floor($s/3600).' h';return date('d/m/Y',strtotime($d));}
function upload_image($field){if(empty($_FILES[$field]['name']))return null;$f=$_FILES[$field];if($f['error']!==UPLOAD_ERR_OK)throw new Exception('No se pudo subir la imagen.');if($f['size']>MAX_UPLOAD)throw new Exception('La imagen supera 5 MB.');$mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;if(!$ext)throw new Exception('Solo JPG, PNG o WEBP.');$name=bin2hex(random_bytes(12)).'.'.$ext;move_uploaded_file($f['tmp_name'],__DIR__.'/../uploads/'.$name);return $name;}
function hamming_similarity($a,$b){
  if(!$a||!$b||strlen($a)!==strlen($b))return null;$same=0;$n=strlen($a);for($i=0;$i<$n;$i++)if($a[$i]===$b[$i])$same++;return $n?($same/$n):null;
}
function cosine_similarity($jsonA,$jsonB){
  if(!$jsonA||!$jsonB)return null;$a=json_decode($jsonA,true);$b=json_decode($jsonB,true);
  if(!is_array($a)||!is_array($b)||count($a)!==count($b)||!count($a))return null;
  $dot=0.0;$na=0.0;$nb=0.0;foreach($a as $i=>$v){$x=(float)$v;$y=(float)$b[$i];$dot+=$x*$y;$na+=$x*$x;$nb+=$y*$y;}
  if($na<=0||$nb<=0)return null;return max(0,min(1,$dot/(sqrt($na)*sqrt($nb))));
}
function ai_label_tokens($labels){
  $labels=mb_strtolower((string)$labels);
  $stop=['hand','held','computer','microcomputer','device','electronic','equipment','object','portable','personal'];
  $tokens=preg_split('/[^a-z0-9áéíóúñ]+/u',$labels,-1,PREG_SPLIT_NO_EMPTY);
  return array_values(array_unique(array_filter($tokens,fn($t)=>mb_strlen($t)>=3 && !in_array($t,$stop,true))));
}
function label_similarity($a,$b){
  $A=ai_label_tokens($a);$B=ai_label_tokens($b);if(!$A||!$B)return null;
  $inter=count(array_intersect($A,$B));$union=count(array_unique(array_merge($A,$B)));
  return $union?($inter/$union):0.0;
}
/*
 * MobileNet puede describir dos fotos del mismo tipo con palabras distintas
 * (por ejemplo "reflex camera" y "digital camera"). Esta capa normaliza las
 * etiquetas reales de la IA a familias visuales. NO usa la categoría escrita
 * por el usuario para decidir si dos objetos pertenecen a la misma familia.
 */
function ai_object_families($labels){
  $s=mb_strtolower((string)$labels);
  $families=[
    'camara'=>['camera','reflex','dslr','digital camera','polaroid','lens','photographic','camcorder'],
    'celular'=>['cellular telephone','cell phone','mobile phone','smartphone','iphone','dial telephone','telephone'],
    'computador'=>['laptop','notebook computer','desktop computer','computer keyboard','computer'],
    'calculadora'=>['calculator','adding machine'],
    'reloj'=>['watch','wristwatch','digital watch','analog clock','stopwatch'],
    'bolso'=>['backpack','rucksack','knapsack','handbag','purse','shoulder bag','shopping bag','briefcase'],
    'billetera'=>['wallet','billfold','pocketbook'],
    'llaves'=>['key','key ring','keychain','padlock'],
    'audifonos'=>['headphone','headset','earphone','earbud'],
    'libro'=>['book jacket','book','notebook','binder','comic book'],
    'botella'=>['water bottle','bottle','pop bottle','beer bottle','thermos'],
    'sombrilla'=>['umbrella','parasol'],
    'gafas'=>['sunglass','sunglasses','spectacles','eyeglass'],
    'zapato'=>['running shoe','sneaker','loafer','sandal','shoe'],
    'ropa'=>['jersey','sweatshirt','shirt','t-shirt','coat','jacket','trench coat','jean','miniskirt','sock'],
    'silla'=>['chair','rocking chair','folding chair','barber chair','throne'],
    'bicicleta'=>['bicycle','mountain bike','tricycle'],
    'casco'=>['helmet','crash helmet','football helmet'],
    'maleta'=>['suitcase','luggage','trunk'],
    'control'=>['remote control','joystick','game controller'],
  ];
  $out=[];
  foreach($families as $family=>$words){foreach($words as $w){if(str_contains($s,$w)){$out[]=$family;break;}}}
  return array_values(array_unique($out));
}
function semantic_family_match($a,$b){
  $A=ai_object_families($a);$B=ai_object_families($b);
  if(!$A||!$B)return ['known'=>false,'same'=>false,'familiesA'=>$A,'familiesB'=>$B];
  return ['known'=>true,'same'=>count(array_intersect($A,$B))>0,'familiesA'=>$A,'familiesB'=>$B];
}
function match_details($a,$b){
  $reasons=[];
  $visual=cosine_similarity($a['ai_embedding']??null,$b['ai_embedding']??null);
  $hash=hamming_similarity($a['image_hash']??null,$b['image_hash']??null);
  $label=label_similarity($a['ai_label']??'',$b['ai_label']??'');
  $sem=semantic_family_match($a['ai_label']??'',$b['ai_label']??'');
  $visualPct=$visual!==null?round($visual*100):null;
  $hashPct=$hash!==null?round($hash*100):null;
  $labelPct=$label!==null?round($label*100):null;

  // Hash solo detecta fotos casi idénticas; no decide similitud entre objetos distintos.
  $samePhoto=($hashPct!==null && $hashPct>=97);
  if($samePhoto){
    return ['total'=>98,'visual'=>$visualPct,'hash'=>$hashPct,'label'=>$labelPct,'image'=>100,'semantic'=>100,'meta'=>0,
      'reasons'=>['fotografía prácticamente idéntica'],'eligible'=>true,'family'=>implode(', ',$sem['familiesA'])];
  }

  // La IA semántica manda: si MobileNet reconoce una familia conocida en ambas fotos
  // y son distintas (cámara vs bolso, silla vs teléfono), se descarta aunque coincidan color/lugar.
  if($sem['known'] && !$sem['same']){
    return ['total'=>0,'visual'=>$visualPct,'hash'=>$hashPct,'label'=>$labelPct,'image'=>$visualPct,'semantic'=>0,'meta'=>0,
      'reasons'=>['la IA reconoce tipos de objeto diferentes'],'eligible'=>false,'family'=>''];
  }

  // Si ambas son de la misma familia reconocida, se muestran incluso si son modelos/marcas distintos.
  // El embedding visual sirve para graduar cuánto se parecen físicamente dentro de esa familia.
  if($sem['known'] && $sem['same']){
    $v=$visualPct??50;
    // Recalibración: misma familia = candidato útil (30-75%), no falsa certeza.
    $shape=max(0,min(100,($v-35)/65*100));
    $total=(int)round(32 + 0.43*$shape); // 32..75 antes de contexto
    $reasons[]='mismo tipo de objeto reconocido por IA';
    if($v>=85)$reasons[]='forma visual muy parecida';
    elseif($v>=65)$reasons[]='apariencia visual relacionada';
    else $reasons[]='modelos o diseños diferentes';
  } else {
    // Para objetos que MobileNet no logra clasificar en una familia conocida, exigimos
    // evidencia visual fuerte. Así no mostramos todos los objetos de la plataforma.
    if($visualPct===null || $visualPct<82){
      return ['total'=>0,'visual'=>$visualPct,'hash'=>$hashPct,'label'=>$labelPct,'image'=>$visualPct,'semantic'=>null,'meta'=>0,
        'reasons'=>['sin similitud visual suficiente'],'eligible'=>false,'family'=>''];
    }
    $total=(int)round(30 + (($visualPct-82)/18)*40); // 30..70
    $reasons[]='patrón visual parecido detectado por IA';
  }

  // Contexto: solo afina. Nunca convierte objetos diferentes en coincidencia.
  $meta=0;
  $ca=mb_strtolower(trim($a['color']??''));$cb=mb_strtolower(trim($b['color']??''));
  if($ca&&$cb&&($ca===$cb||str_contains($ca,$cb)||str_contains($cb,$ca))){$meta+=3;$reasons[]='color compatible';}
  $la=mb_strtolower(trim($a['location']??''));$lb=mb_strtolower(trim($b['location']??''));
  if($la&&$lb&&(str_contains($la,$lb)||str_contains($lb,$la))){$meta+=2;$reasons[]='lugar relacionado';}
  $days=abs((strtotime($a['event_date'])-strtotime($b['event_date']))/86400);
  if($days<=1){$meta+=4;$reasons[]='fecha muy cercana';}elseif($days<=3){$meta+=2;$reasons[]='fecha cercana';}
  // La categoría declarada NO se usa para admitir ni descartar candidatos; solo puede sumar 1 punto.
  $catA=mb_strtolower(trim($a['category']??''));$catB=mb_strtolower(trim($b['category']??''));
  if($catA&&$catA===$catB){$meta+=1;$reasons[]='categoría declarada compatible';}
  $total=min(95,$total+$meta);
  return ['total'=>$total,'visual'=>$visualPct,'hash'=>$hashPct,'label'=>$labelPct,'image'=>$visualPct,'semantic'=>$sem['same']?100:null,
    'meta'=>$meta,'reasons'=>$reasons,'eligible'=>true,'family'=>implode(', ',$sem['familiesA'])];
}
function resolved_label($r){return 'RESUELTO';}
