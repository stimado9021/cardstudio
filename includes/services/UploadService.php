<?php
// includes/services/UploadService.php
// Servicio de uploads - validación y manejo de archivos

class UploadService {
    private $uploadDir;
    private $thumbDir;
    private $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    private $maxSize = 5 * 1024 * 1024; // 5MB

    public function __construct($uploadDir = 'admin/uploads') {
        $this->uploadDir = $uploadDir;
        $this->thumbDir = $uploadDir . '/thumbnails';
        $this->ensureDirectories();
    }

    /**
     * Crear directorios si no existen
     */
    private function ensureDirectories() {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        if (!file_exists($this->thumbDir)) {
            mkdir($this->thumbDir, 0755, true);
        }
    }

    /**
     * Validar un archivo subido
     */
    public function validateUpload($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Error al subir el archivo'];
        }

        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return ['valid' => false, 'error' => 'Tipo de archivo no permitido. Use JPG, PNG o WebP.'];
        }

        // Validar tamaño
        if ($file['size'] > $this->maxSize) {
            return ['valid' => false, 'error' => 'La imagen no puede superar 5MB.'];
        }

        // Validar MIME type real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedMimes)) {
            return ['valid' => false, 'error' => 'El archivo no es una imagen válida.'];
        }

        return ['valid' => true, 'extension' => $extension];
    }

    /**
     * Subir imagen de fondo
     */
    public function uploadBackground($file) {
        $validation = $this->validateUpload($file);
        if (!$validation['valid']) {
            return $validation;
        }

        $extension = $validation['extension'];
        $nombre = "fondo_" . uniqid() . "." . $extension;
        $ruta = $this->uploadDir . "/" . $nombre;

        if (!move_uploaded_file($file['tmp_name'], $ruta)) {
            return ['valid' => false, 'error' => 'Error al guardar la imagen'];
        }

        return ['valid' => true, 'path' => $ruta];
    }

    /**
     * Guardar miniatura desde base64
     */
    public function saveThumbnail($base64Data) {
        $imgData = str_replace('data:image/jpeg;base64,', '', $base64Data);
        $imgData = str_replace(' ', '+', $imgData);
        $imgDecoded = base64_decode($imgData);

        if ($imgDecoded === false) {
            return ['valid' => false, 'error' => 'Datos de miniatura inválidos'];
        }

        $nombre = "thumb_" . uniqid() . ".jpg";
        $ruta = $this->thumbDir . "/" . $nombre;

        file_put_contents($ruta, $imgDecoded);

        return ['valid' => true, 'path' => $ruta];
    }

    /**
     * Eliminar un archivo
     */
    public function deleteFile($path) {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}
?>
