<?php

/** Conversión única entre la cuadrícula de diseño y el lienzo 4:3 real. */
class PptCoordinateConverter
{
    const TEMPLATE_WIDTH = 1920;
    const TEMPLATE_HEIGHT = 1080;
    const SLIDE_WIDTH = 25.40;
    const SLIDE_HEIGHT = 19.05;
    const PHP_PIXELS_PER_CM = 37.795275591;

    public static function x($pixel) { return ($pixel / self::TEMPLATE_WIDTH) * self::SLIDE_WIDTH; }
    public static function y($pixel) { return ($pixel / self::TEMPLATE_HEIGHT) * self::SLIDE_HEIGHT; }
    public static function width($pixel) { return ($pixel / self::TEMPLATE_WIDTH) * self::SLIDE_WIDTH; }
    public static function height($pixel) { return ($pixel / self::TEMPLATE_HEIGHT) * self::SLIDE_HEIGHT; }
    public static function phpPixels($cm) { return (int) round($cm * self::PHP_PIXELS_PER_CM); }

    /** Convierte un rectángulo de plantilla y valida su posición física final. */
    public static function rectangle($x, $y, $width, $height)
    {
        $rect = ['x'=>self::x($x), 'y'=>self::y($y), 'width'=>self::width($width), 'height'=>self::height($height)];
        self::validateElement($rect);
        return $rect;
    }

    public static function validateElement(array $element)
    {
        if ($element['x'] < 0 || $element['y'] < 0 || $element['x'] + $element['width'] > self::SLIDE_WIDTH || $element['y'] + $element['height'] > self::SLIDE_HEIGHT) {
            throw new \RuntimeException(sprintf('Elemento fuera del lienzo: X=%.2f, Y=%.2f, ancho=%.2f, alto=%.2f cm; máximo 25.40 × 19.05 cm.', $element['x'], $element['y'], $element['width'], $element['height']));
        }
    }
}
