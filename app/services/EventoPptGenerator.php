<?php

/** Generador 4:3: todas las coordenadas de este archivo están en centímetros. */
class EventoPptGenerator
{
    private $ppt, $background, $logo, $direccion, $slideNumber = 0;
    const X = 1.50, W = 22.40, Y = 3.00, BOTTOM = 16.40;

    public function __construct($background, $logo = null, $direccion = '')
    {
        $this->ppt = new \PhpOffice\PhpPresentation\PhpPresentation();
        $this->ppt->getLayout()->setDocumentLayout(\PhpOffice\PhpPresentation\DocumentLayout::LAYOUT_SCREEN_4X3);
        $this->ppt->removeSlideByIndex(0); $this->background = $background; $this->logo = $logo; $this->direccion = $direccion;
    }

    public function build(array $d)
    {
        $this->crearDiapositivaPortada($d); $this->crearDiapositivaObjetivo($d); $this->crearDiapositivaJustificacion($d);
        $this->crearDiapositivaGenerales($d); $this->crearDiapositivaUbicacion($d); $this->crearDiapositivaOrdenDia($d['agenda'] ?? []);
        $this->crearDiapositivaPresidium($d); $this->crearDiapositivaInvitados($d['invitados'] ?? []); $this->crearDiapositivaModulos($d['modulos'] ?? []);
        $this->crearDiapositivaRequerimientosConsolidados($d); $this->crearDiapositivaResumenRequerimientos($d);
        return $this->ppt;
    }

    public function crearDiapositivaPortada(array $d)
    {
        $t = $this->coverSlide($d); if (!empty($d['portada_fondo'])) $t->addFullSlideLayer($d['portada_fondo'], true); if (!empty($d['portada_template'])) $t->addFullSlideLayer($d['portada_template'], true);
        $t->addTextBox($d['evento_nombre'] ?? 'Evento', 1.8, 3.0, 21.8, 2.4, 28, 'center', ['name'=>'Título de portada','bold'=>true,'color'=>EventoPptTemplate::INSTITUTIONAL,'max'=>28,'min'=>18]);
        $t->addTextBox('Aprobado: '.($d['director'] ?? ''), 2.0, 6.55, 10.5, .65, 13, 'center', ['name'=>'Aprobado','max'=>13,'min'=>10]);
        $t->addTextBox('Responsable: '.($d['responsable_por'] ?? ''), 12.9, 6.55, 10.5, .65, 13, 'center', ['name'=>'Responsable','max'=>13,'min'=>10]);
    }
    public function crearDiapositivaObjetivo(array $d){$this->campos('Objetivo y beneficiarios',[['Línea de acción PbRM',$d['linea_accion']??''],['Objetivo PbRM',$d['objetivo_pbrm']??''],['Objetivo del evento',$d['objetivo_evento']??''],['Beneficiarios',(string)($d['num_beneficiarios']??'')]],15);}
    public function crearDiapositivaJustificacion(array $d){$this->campos('Justificación e impacto',[['Justificación e impacto',$d['justificacion']??'Sin información registrada.']],16);}
    public function crearDiapositivaGenerales(array $d){$this->campos('Generales del evento',[['Fecha',$d['fecha_evento']??''],['Horario',$d['evento_horario']??''],['Lugar',$d['ubicacion']??''],['Vestimenta',$d['vestimenta']??''],['Coordinación',$d['coordinacion']??''],['Responsable',$d['responsable_por']??''],['Duración',$d['duracion']??'']],14);}
    public function crearDiapositivaUbicacion(array $d){$t=$this->slide('Ubicación del evento');$t->addTextBox('Dirección: '.($d['ubicacion']??''),self::X,3.0,self::W,.72,14,'left',['name'=>'Dirección','max'=>14,'min'=>10]);$t->addTextBox('Mapa: '.($d['link_mapa']??''),self::X,3.8,self::W,.55,11,'left',['name'=>'Mapa','color'=>'FF555555','max'=>11,'min'=>9]);if(!$t->addImage($d['imagen_lugar']??null,1.8,4.7,10.0,10.8,true))$t->addTextBox('Foto del lugar no disponible.',1.8,4.7,10.0,10.8,14,'center',['name'=>'Aviso foto lugar','color'=>'FF666666','max'=>14,'min'=>10]);if(!$t->addImage($d['imagen_maps']??null,13.6,4.7,9.9,10.8,true))$t->addTextBox('Foto de Google Maps no disponible.',13.6,4.7,9.9,10.8,14,'center',['name'=>'Aviso foto mapa','color'=>'FF666666','max'=>14,'min'=>10]);}
    public function crearDiapositivaOrdenDia(array $rows){$data=array_map(function($r){return [substr($r['hora_inicio']??'',0,5).' - '.substr($r['hora_fin']??'',0,5),$r['actividad']??'', $r['otro_responsable']??($r['responsable_nombre']??''),($r['duracion_calculada']??'').' min'];},$rows);$this->tabla('Orden del día',['Hora','Actividad','Responsable','Duración'],$data,[4.30,7.53,6.45,3.22]);}
    public function crearDiapositivaInvitados(array $rows){$this->tabla('PERSONAS INVITADAS ESPECIALES',['N°','Persona invitada'],array_map(function($r,$i){return [$i+1,($r['nombre']??'')."\n".($r['cargo']??'')];},$rows,array_keys($rows)),[2.00,19.40]);}
    public function crearDiapositivaModulos(array $rows){$this->tabla('Módulos de jornada',['Núm.','Institución','Servicio'],array_map(function($r,$i){return [$i+1,$r['institucion']??($r['nombre_institucion']??''),$r['servicio']??''];},$rows,array_keys($rows)),[2.00,9.80,9.60]);}
    public function crearDiapositivaPresidium(array $d){$rows=$d['presidium']??[];usort($rows,function($a,$b){return ($a['orden']??0)<=>($b['orden']??0);});array_unshift($rows,['nombre_invitado'=>'Lcdo. Ricardo Moreno Bastida','cargo_invitado'=>'Presidente Municipal Constitucional de Toluca','orden'=>0]);$t=$this->slide('Presidium');if(!$t->addImage($d['presidium_background']??null,1.8,3.2,10.0,10.8,true))$t->addTextBox('Ilustración del acomodo de presidium no disponible.',1.8,3.2,10.0,10.8,14,'center',['name'=>'Aviso ilustración presidium','color'=>'FF666666','max'=>14,'min'=>10]);foreach($rows as $i=>$r){$row=intdiv($i,2);if($row>4)break;$x=$i%2?18.5:12.2;$y=3.25+$row*2.45;$marca=$i===0?'*':(string)($r['orden']??$i);$t->addTextBox($marca.'. '.($r['nombre_invitado']??''),$x,$y,4.9,.65,12,'center',['name'=>'Nombre de presidium','bold'=>true,'color'=>EventoPptTemplate::INSTITUTIONAL,'max'=>12,'min'=>9]);$t->addTextBox($r['cargo_invitado']??'',$x,$y+.72,4.9,.92,10,'center',['name'=>'Cargo de presidium','max'=>10,'min'=>8]);}$t->addTextBox('Maestra de ceremonias: '.($d['maestra_ceremonias']??''),12.2,15.2,11.2,.55,11,'left',['name'=>'Maestra de ceremonias','bold'=>true,'max'=>11,'min'=>9]);}
    public function crearDiapositivaRequerimientosConsolidados(array $d){$t=$this->slide('REQUERIMIENTOS');$t->addTable(['Delegación Administrativa','Comunicación Social','Dirección General de Administración'],[[$this->listaRequerimientos($d['req_internos']??[]),$this->listaRequerimientos($d['req_comunicacion']??[]),$this->listaRequerimientos($d['req_externos']??[])]],[7.13,7.13,7.13],1.8,3.2,12.8);}
    public function crearDiapositivaResumenRequerimientos(array $d){$t=$this->slide('REQUERIMIENTOS');$detalle='Evento: '.($d['evento_nombre']??'')."\n\nDía: ".$this->fechaLarga($d['fecha_evento']??'')."\n\nHorario: ".($d['evento_horario']??'')." horas\n\nUbicación: ".($d['ubicacion']??'');$t->addTable(['Delegación Administrativa','Evento y ubicación'],[[$this->listaRequerimientos($d['req_internos']??[]),$detalle]],[10.2,10.2],1.8,3.2,8.6);$this->bloqueFirma($t,'Responsable del Evento',$d['firma_responsable_nombre']??'',$d['firma_responsable_cargo']??'',1.8,'Vo. Bo.');$this->bloqueFirma($t,'Delegado Administrativo',$d['firma_delegado_nombre']??'',$d['firma_delegado_cargo']??'',13.0,'Vo. Bo. Validado');}

    private function listaRequerimientos(array $rows){$items=[];foreach($rows as $r){$cantidad=$r['cantidad']??($r['cantidad_solicitada']??'');$nombre=$r['nombre_insumo']??'';$dimension=trim(($r['medida']??'').' '.($r['unidad']??''));if($nombre!=='')$items[]=$cantidad.'  '.$nombre.($dimension!==''?"\n   ".$dimension:'');}return $items?implode("\n\n",$items):'Sin requerimientos registrados.';}
    private function fechaLarga($fecha){$marca=strtotime((string)$fecha);return $marca?date('d',$marca).' de '.['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][(int)date('n',$marca)-1].' de '.date('Y',$marca):(string)$fecha;}
    private function bloqueFirma($t,$titulo,$nombre,$cargo,$x,$vobo){$t->addTextBox($titulo,$x,12.55,9.4,.4,11,'center',['name'=>'Título firma','bold'=>true,'max'=>11,'min'=>9]);$t->addTextBox('________________________',$x,13.08,9.4,.35,11,'center',['name'=>'Línea firma','max'=>11,'min'=>9]);$t->addTextBox($nombre?:'Sin persona autorizada registrada.',$x,13.52,9.4,.5,11,'center',['name'=>'Nombre firma','bold'=>true,'max'=>11,'min'=>9]);$t->addTextBox($cargo?:'Cargo no registrado.',$x,14.08,9.4,.5,10,'center',['name'=>'Cargo firma','max'=>10,'min'=>8]);$t->addTextBox($vobo,$x,14.72,9.4,.4,10,'center',['name'=>'Validación firma','bold'=>true,'max'=>10,'min'=>8]);}

    private function tabla($title,array $headers,array $rows,array $widths){if(array_sum($widths)>21.50)throw new \RuntimeException('La tabla '.$title.' excede el ancho máximo de 21.50 cm.');if(!$rows)$rows=[array_fill(0,count($headers),'Sin información registrada.')];$page=[];$height=.62;foreach($rows as $row){$rowH=$this->rowHeight($row,$widths);if($height+$rowH>12.8&&$page){$this->drawTable($title,$headers,$page,$widths);$page=[];$height=.62;}$page[]=$row;$height+=$rowH;}$this->drawTable($title,$headers,$page,$widths);}
    private function drawTable($title,$headers,$rows,$widths){$t=$this->slide($title);$t->addTable($headers,$rows,$widths,1.95,3.0,12.8);}
    private function campos($title,array $items,$font){$page=1;$t=$this->slide($title);$y=3.0;foreach($items as $item){foreach($this->chunks($item[1]?:'Sin información registrada.',self::W,$font,3.2) as $n=>$text){$h=$this->textHeight($text,self::W,$font)+.25;if($y+.45+$h>self::BOTTOM){$page++;$t=$this->slide($title.' · continuación '.$page);$y=3.0;}$t->addTextBox($n?$item[0].' (continuación)':$item[0],self::X,$y,self::W,.35,12,'left',['name'=>'Etiqueta','bold'=>true,'color'=>EventoPptTemplate::INSTITUTIONAL,'max'=>12,'min'=>10]);$y+=.45;$t->addTextBox($text,self::X,$y,self::W,$h,$font,'left',['name'=>'Contenido','max'=>$font,'min'=>10]);$y+=$h+.35;}}}
    private function chunks($text,$width,$font,$maxH){$lineChars=max(12,(int)(($width*37.795)/($font*.55)));$lines=explode("\n",wordwrap((string)$text,$lineChars,"\n",false));$per=max(1,(int)floor(($maxH*37.795)/($font*1.3)));return array_map(function($g){return implode("\n",$g);},array_chunk($lines,$per));}
    private function textHeight($text,$width,$font){$chars=max(12,(int)(($width*37.795)/($font*.55)));return max(.55,(int)ceil(mb_strlen((string)$text,'UTF-8')/$chars)*$font*1.3/37.795);}
    private function rowHeight($row,$widths){$lines=1;foreach($row as $i=>$v)$lines=max($lines,(int)ceil(mb_strlen((string)$v,'UTF-8')/max(12,($widths[$i]*37.795)/8.5)));return max(.58,.18+$lines*.34);}
    private function slide($title=null){$this->slideNumber++;$t=new EventoPptTemplate($this->ppt->createSlide(),$this->background,$this->direccion,$this->slideNumber);if($title)$t->addSlideTitle($title);return $t;}
    private function coverSlide(array $data){$this->slideNumber++;return new EventoPptTemplate($this->ppt->createSlide(),null,$this->direccion,$this->slideNumber);}
}
