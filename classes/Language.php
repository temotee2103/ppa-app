<?php
/**
 * 语言处理类
 * 处理多语言文本翻译和切换
 */
class Language {
    private static $instance = null;
    private $translations = [];
    private $current_lang;
    
    /**
     * 构造函数 - 加载当前语言的翻译
     */
    private function __construct() {
        global $config;
        
        // 从会话中获取当前语言
        $this->current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $config['default_lang'];
        
        // 验证语言是否有效
        if (!array_key_exists($this->current_lang, $config['available_langs'])) {
            $this->current_lang = $config['default_lang'];
            $_SESSION['lang'] = $this->current_lang;
        }
        
        // 加载当前语言的翻译文件
        $this->loadTranslations();
    }
    
    /**
     * 获取单例实例
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 加载指定语言的翻译文件
     */
    private function loadTranslations() {
        global $config;
        
        $lang_path = $config['languages_path'] . $this->current_lang;
        
        // 检查语言目录是否存在
        if (!is_dir($lang_path)) {
            error_log("Language directory not found: " . $lang_path);
            return;
        }
        
        // 遍历语言目录中的所有PHP文件
        $lang_files = glob($lang_path . "/*.php");
        foreach ($lang_files as $file) {
            $translations = include $file;
            if (is_array($translations)) {
                $this->translations = array_merge($this->translations, $translations);
            }
        }
    }
    
    /**
     * 获取翻译文本
     */
    public function get($key, $default = null) {
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }
        
        // 如果未找到翻译，返回默认值或键名
        return $default !== null ? $default : $key;
    }
    
    /**
     * 设置当前语言
     */
    public function setLanguage($lang) {
        global $config;
        
        // 验证语言是否有效
        if (!array_key_exists($lang, $config['available_langs'])) {
            return false;
        }
        
        // 更新当前语言
        $this->current_lang = $lang;
        $_SESSION['lang'] = $lang;
        
        // 重新加载翻译
        $this->translations = [];
        $this->loadTranslations();
        
        return true;
    }
    
    /**
     * 获取当前语言代码
     */
    public function getCurrentLanguage() {
        return $this->current_lang;
    }
    
    /**
     * 获取所有可用语言
     */
    public function getAvailableLanguages() {
        global $config;
        return $config['available_langs'];
    }
} 