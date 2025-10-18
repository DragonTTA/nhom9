<?php

if (!function_exists('toast')) {
    /**
     * Hiển thị thông báo toastr ở view kế tiếp
     *
     * @param string $message - nội dung thông báo
     * @param string $type - loại thông báo (success, info, warning, error)
     */
    function toast($message, $type = 'success')
    {
        session()->flash('toast', [
            'type' => $type,
            'message' => $message
        ]);
    }
}
