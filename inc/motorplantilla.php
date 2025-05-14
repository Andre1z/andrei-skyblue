<?php
/**
 * motorplantilla.php
 *
 * Este motor de plantillas procesa archivos de plantilla y los combina con datos dinámicos.
 * Utiliza sintaxis de marcadores personalizados:
 *   - Placeholders simples: {{clave}}
 *   - Bloques de bucle: 
 *         <!-- BEGIN: lista --> 
 *             ... {{campo}} ... 
 *         <!-- END: lista -->
 *   - Inyección de atributos: Placeholders del tipo {{ATTR:tag}}
 *
 * La función principal, render(), carga la plantilla (local o remota), procesa los bloques, reemplaza
 * los placeholders y, finalmente, inyecta atributos según se especifique.
 */

class MotorPlantilla {

    /**
     * Procesa y retorna la plantilla combinada con datos y atributos.
     *
     * @param string $templatePath Ruta o URL de la plantilla.
     * @param array  $data         Datos para reemplazar en la plantilla.
     * @param array  $attributes   Atributos a inyectar en la plantilla, organizados por etiqueta.
     *
     * @return string Plantilla renderizada o mensaje de error.
     */
    public function render(string $templatePath, array $data = [], array $attributes = []): string {
        // Cargar la plantilla (ya sea desde un archivo local o una URL remota).
        $template = $this->loadTemplate($templatePath);
        if ($template === false) {
            return "Error: No se pudo cargar la plantilla '{$templatePath}'.";
        }
        
        // Procesar bloques de bucle (para arrays).
        $template = $this->applyLoops($template, $data);
        
        // Reemplazo simple de placeholders: {{clave}}.
        $template = $this->applyPlaceholders($template, $data);
        
        // Inyectar atributos mediante placeholders especiales: {{ATTR:tag}}.
        $template = $this->applyAttributes($template, $attributes);
        
        return $template;
    }

    /**
     * Carga el contenido de la plantilla desde una ruta local o una URL.
     *
     * @param string $templatePath Ruta o URL de la plantilla.
     *
     * @return string|false Contenido de la plantilla o false en caso de error.
     */
    private function loadTemplate(string $templatePath) {
        if (filter_var($templatePath, FILTER_VALIDATE_URL)) {
            $content = @file_get_contents($templatePath);
        } else {
            if (!file_exists($templatePath)) {
                return false;
            }
            $content = file_get_contents($templatePath);
        }
        return $content;
    }

    /**
     * Reemplaza los placeholders simples ({{clave}}) en la plantilla usando los datos provistos.
     *
     * @param string $template La plantilla a procesar.
     * @param array  $data     Datos para reemplazar.
     *
     * @return string Plantilla con placeholders simples reemplazados.
     */
    private function applyPlaceholders(string $template, array $data): string {
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                // Reemplaza todas las ocurrencias de {{clave}} por su valor.
                $template = str_replace('{{' . $key . '}}', htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'), $template);
            }
        }
        return $template;
    }

    /**
     * Procesa bloques de bucle delimitados por:
     *   <!-- BEGIN: nombre_bloque -->
     *        [contenido con {{campo}}]
     *   <!-- END: nombre_bloque -->
     *
     * Para cada elemento del arreglo $data[nombre_bloque], se repite el bloque aplicando reemplazos.
     *
     * @param string $template La plantilla a procesar.
     * @param array  $data     Datos para los bloques.
     *
     * @return string Plantilla con los bloques procesados.
     */
    private function applyLoops(string $template, array $data): string {
        // Patrón que detecta los bloques de bucle.
        $pattern = '/<!--\s*BEGIN:\s*(\w+)\s*-->(.*?)<!--\s*END:\s*\1\s*-->/s';
        return preg_replace_callback($pattern, function ($match) use ($data) {
            $blockName    = $match[1];
            $blockContent = $match[2];
            $result       = '';
            if (isset($data[$blockName]) && is_array($data[$blockName])) {
                foreach ($data[$blockName] as $item) {
                    $currentBlock = $blockContent;
                    // Si el item es un arreglo, se reemplazan sus campos.
                    if (is_array($item)) {
                        foreach ($item as $varKey => $varValue) {
                            $currentBlock = str_replace('{{' . $varKey . '}}', htmlspecialchars((string)$varValue, ENT_QUOTES, 'UTF-8'), $currentBlock);
                        }
                    }
                    $result .= $currentBlock;
                }
            }
            return $result;
        }, $template);
    }

    /**
     * Inyecta atributos en la plantilla usando marcadores especiales: {{ATTR:tag}}.
     * Para cada etiqueta (por ejemplo, "form"), se espera que en la plantilla exista este marcador
     * que se reemplazará por una cadena de atributos.
     *
     * @param string $template   La plantilla a procesar.
     * @param array  $attributes Atributos organizados en formato: [ 'tag' => [ 'attr' => 'valor', ... ] ]
     *
     * @return string Plantilla con los atributos inyectados.
     */
    private function applyAttributes(string $template, array $attributes): string {
        foreach ($attributes as $tag => $attrs) {
            $attrString = '';
            if (is_array($attrs)) {
                foreach ($attrs as $attrName => $attrVal) {
                    $attrString .= " " . $attrName . "=\"" . htmlspecialchars((string)$attrVal, ENT_QUOTES, 'UTF-8') . "\"";
                }
            }
            // Reemplaza el marcador específico de este tag.
            $template = str_replace('{{ATTR:' . $tag . '}}', $attrString, $template);
        }
        return $template;
    }
}
?>