<?php

require_once dirname(__DIR__, 2) . '/helpers/PptCoordinateConverter.php';

/** Todas las coordenadas públicas de esta clase están expresadas en centímetros. */
class EventoPptTemplate
{
    const SLIDE_WIDTH = 25.40;
    const SLIDE_HEIGHT = 19.05;
    const MARGIN_LEFT = 1.50;
    const MARGIN_RIGHT = 1.50;
    const MARGIN_TOP = 1.00;
    const MARGIN_BOTTOM = 1.00;
    const CONTENT_X = 1.50;
    const CONTENT_Y = 3.00;
    const CONTENT_W = 22.40;
    const CONTENT_BOTTOM = 16.40;
    const FOOTER_Y = 17.20;
    const FONT = 'Aptos';
    const COLOR = 'FF3E3E3E';
    const INSTITUTIONAL = 'FF800000';

    private $slide, $slideNumber;

    public function __construct($slide, $background, $direccion = '', $slideNumber = 1)
    {
        $this->slide = $slide; $this->slideNumber = $slideNumber;
        $this->setBackground($background);
    }

    public function getContentArea() { return ['x'=>self::CONTENT_X,'y'=>self::CONTENT_Y,'width'=>self::CONTENT_W,'height'=>self::CONTENT_BOTTOM-self::CONTENT_Y,'bottom'=>self::CONTENT_BOTTOM]; }
    public function cmToPx($cm) { return PptCoordinateConverter::phpPixels($cm); }

    public function setBackground($path)
    {
        if (!$this->isImage($path)) return false;
        $background = new \PhpOffice\PhpPresentation\Slide\Background\Image(); $background->setPath($path); $this->slide->setBackground($background); return true;
    }

    /** Capa a pantalla completa para portada: se usa antes de cualquier texto. */
    public function addFullSlideLayer($path, $keepProportion = true)
    {
        if (!$this->isImage($path)) return false;
        $info = getimagesize($path); if (!$info) return false;
        $w = self::SLIDE_WIDTH; $h = self::SLIDE_HEIGHT; $x = 0; $y = 0;
        if ($keepProportion) { $ratio = min($w / $info[0], $h / $info[1]); $drawW=$info[0]*$ratio; $drawH=$info[1]*$ratio; $x=($w-$drawW)/2; $y=($h-$drawH)/2; } else { $drawW=$w; $drawH=$h; }
        return $this->slide->createDrawingShape()->setPath($path)->setOffsetX($this->cmToPx($x))->setOffsetY($this->cmToPx($y))->setWidth($this->cmToPx($drawW))->setHeight($this->cmToPx($drawH));
    }

    public function addSlideTitle($title)
    {
        return $this->addTextBox($title, 1.50, 2.12, 22.40, .55, 23, 'left', ['name'=>'Título','bold'=>true,'color'=>self::INSTITUTIONAL,'max'=>23,'min'=>16,'zone'=>'title','singleLine'=>true]);
    }

    public function addTextBox($text, $x, $y, $width, $height, $fontSize, $alignment = 'left', array $style = [])
    {
        $this->validate($style['name'] ?? 'Cuadro de texto', $x, $y, $width, $height, $style['zone'] ?? 'content');
        $size=$this->fitFont((string)$text,$width,$height,$style['max']??$fontSize,$style['min']??10);
        $shape=$this->slide->createRichTextShape()->setOffsetX($this->cmToPx($x))->setOffsetY($this->cmToPx($y))->setWidth($this->cmToPx($width))->setHeight($this->cmToPx($height));
        $align=$alignment==='center'?\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER:($alignment==='right'?\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_RIGHT:\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_LEFT);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal($align);
        $run=$shape->createTextRun(!empty($style['singleLine'])?$this->truncate((string)$text,max(1,(int)(($width*PptCoordinateConverter::PHP_PIXELS_PER_CM)/($size*.55)))):$this->wrap((string)$text,$width,$size));
        $run->getFont()->setName($style['font']??self::FONT)->setSize($size)->setBold(!empty($style['bold']))->setColor(new \PhpOffice\PhpPresentation\Style\Color($style['color']??self::COLOR));
        if (!empty($style['fill'])) $shape->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color($style['fill']));
        if (!empty($style['border'])) $shape->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE)->setColor(new \PhpOffice\PhpPresentation\Style\Color($style['border']));
        return $shape;
    }

    public function addImage($path, $x, $y, $width, $height, $keepProportion = true)
    {
        if (!$this->isImage($path)) return false; $this->validate('Imagen', $x, $y, $width, $height, 'content'); $info=getimagesize($path); if(!$info)return false;
        if($keepProportion){$ratio=min($width/$info[0],$height/$info[1]);$drawW=$info[0]*$ratio;$drawH=$info[1]*$ratio;$x+=($width-$drawW)/2;$y+=($height-$drawH)/2;}else{$drawW=$width;$drawH=$height;}
        return $this->slide->createDrawingShape()->setPath($path)->setOffsetX($this->cmToPx($x))->setOffsetY($this->cmToPx($y))->setWidth($this->cmToPx($drawW))->setHeight($this->cmToPx($drawH));
    }

    public function addTable(array $headers,array $rows,array $widths,$x,$y,$maxHeight)
    {
        $headerH=.62;$used=$headerH;$cursor=$x;
        foreach($headers as $i=>$header){$this->cell($header,$cursor,$y,$widths[$i],$headerH,true);$cursor+=$widths[$i];}$y+=$headerH;
        foreach($rows as $row){$rowH=$this->tableRowHeight($row,$widths);if($used+$rowH>$maxHeight)break;$cursor=$x;foreach($headers as $i=>$header){$this->cell($row[$i]??'',$cursor,$y,$widths[$i],$rowH);$cursor+=$widths[$i];}$y+=$rowH;$used+=$rowH;}
    }

    public function tableRowHeight(array $row,array $widths)
    {
        $lines=1;foreach($row as $i=>$value)$lines=max($lines,(int)ceil(mb_strlen((string)$value,'UTF-8')/max(12,(($widths[$i]??3)*PptCoordinateConverter::PHP_PIXELS_PER_CM)/8.5)));return max(.58,.18+$lines*.34);
    }

    private function cell($text,$x,$y,$w,$h,$header=false)
    {
        return $this->addTextBox($text,$x+.08,$y+.06,$w-.16,$h-.12,$header?11:10,$header?'center':'left',['name'=>$header?'Encabezado de tabla':'Celda de tabla','bold'=>$header,'color'=>$header?'FFFFFFFF':self::COLOR,'fill'=>$header?self::INSTITUTIONAL:null,'border'=>'FFD8C7C7','max'=>$header?11:10,'min'=>8]);
    }
    private function validate($name,$x,$y,$w,$h,$zone)
    {
        $bounds=$this->bounds($zone);$right=$x+$w;$bottom=$y+$h;
        if($x<$bounds['left']||$y<$bounds['top']||$right>$bounds['right']||$bottom>$bounds['bottom'])throw new \RuntimeException(sprintf('Elemento fuera del lienzo: %s. Diapositiva: %d. Coordenada: X=%.2f, Y=%.2f. Dimensión: %.2f×%.2f cm. Máximo permitido: X=%.2f..%.2f, Y=%.2f..%.2f cm.',$name,$this->slideNumber,$x,$y,$w,$h,$bounds['left'],$bounds['right'],$bounds['top'],$bounds['bottom']));
    }
    private function bounds($zone){if($zone==='header')return ['left'=>1.5,'top'=>1.0,'right'=>23.9,'bottom'=>1.75];if($zone==='title')return ['left'=>1.5,'top'=>1.95,'right'=>23.9,'bottom'=>2.75];if($zone==='footer')return ['left'=>1.5,'top'=>17.05,'right'=>23.9,'bottom'=>18.05];return ['left'=>self::MARGIN_LEFT,'top'=>self::CONTENT_Y,'right'=>self::SLIDE_WIDTH-self::MARGIN_RIGHT,'bottom'=>self::CONTENT_BOTTOM];}
    private function isImage($path){$info=$path&&is_file($path)?@getimagesize($path):false;return $info&&in_array($info[2],[IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_GIF],true);}
    private function fitFont($text,$width,$height,$max,$min){for($s=(int)$max;$s>=$min;$s--){$chars=max(12,(int)(($width*PptCoordinateConverter::PHP_PIXELS_PER_CM)/($s*.55)));$lines=max(1,(int)ceil(mb_strlen($text,'UTF-8')/$chars));if($lines*$s*1.3<=($height*PptCoordinateConverter::PHP_PIXELS_PER_CM))return $s;}return $min;}
    private function wrap($text,$width,$font){return wordwrap($text,max(12,(int)(($width*PptCoordinateConverter::PHP_PIXELS_PER_CM)/($font*.55))),"\n",false);}
    private function truncate($text,$limit){return mb_strlen($text,'UTF-8')>$limit?rtrim(mb_substr($text,0,max(1,$limit-1),'UTF-8')).'…':$text;}
}
