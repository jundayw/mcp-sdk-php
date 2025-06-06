<?php

/**
 * Model Context Protocol SDK for PHP
 *
 * (c) 2025 Logiscape LLC <https://logiscape.com>
 *
 * Developed by:
 * - Josh Abbott
 * - Claude 3.7 Sonnet (Anthropic AI model)
 * - ChatGPT o1 pro mode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    logiscape/mcp-sdk-php 
 * @author     Josh Abbott <https://joshabbott.com>
 * @copyright  Logiscape LLC
 * @license    MIT License
 * @link       https://github.com/logiscape/mcp-sdk-php
 *
 * Filename: Server/Transport/Http/Config.php
 */

declare(strict_types=1);

namespace Mcp\Server\Transport\Http;

/**
 * Configuration management for HTTP transport.
 * 
 * This class manages the configuration options for the HTTP transport,
 * including auto-detection of environment capabilities.
 */
class Config
{
    /**
     * Default configuration options.
     *
     * @var array
     */
    private array $options = [
        'session_timeout' => 3600,     // 1 hour default
        'enable_sse' => false,         // Default disabled for compatibility
        'max_queue_size' => 1000,      // Maximum messages in queue
        'auto_detect' => true,         // Auto-detect environment
        'shared_hosting' => null,      // Force shared hosting mode (null = auto-detect)
        'session_handler' => 'auto',   // Session handler (auto, file, database, memory)
        'host' => 'localhost',         // Server host for CLI mode
        'port' => 8080,                // Server port for CLI mode
        'server_header' => 'MCP-PHP-Server/1.0', // Server identification
    ];
    
    /**
     * Constructor.
     *
     * @param array $customOptions Custom configuration options
     */
    public function __construct(array $customOptions = [])
    {
        if (isset($customOptions['auto_detect']) && $customOptions['auto_detect'] === true) {
            $this->autoDetectOptions();
        } elseif (!isset($customOptions['auto_detect']) && $this->options['auto_detect']) {
            $this->autoDetectOptions();
        }
        
        // Merge custom options
        $this->options = array_merge($this->options, $customOptions);
    }
    
    /**
     * Get a configuration option.
     *
     * @param string $key Option key
     * @return mixed|null Option value or null if not found
     */
    public function get(string $key)
    {
        return $this->options[$key] ?? null;
    }
    
    /**
     * Set a configuration option.
     *
     * @param string $key Option key
     * @param mixed $value Option value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->options[$key] = $value;
    }
    
    /**
     * Get all configuration options.
     *
     * @return array All configuration options
     */
    public function all(): array
    {
        return $this->options;
    }
    
    /**
     * Check if SSE is enabled.
     *
     * @return bool True if SSE is enabled
     */
    public function isSseEnabled(): bool
    {
        return (bool)$this->options['enable_sse'];
    }
    
    /**
     * Check if running in shared hosting mode.
     *
     * @return bool True if in shared hosting mode
     */
    public function isSharedHosting(): bool
    {
        // If explicitly set, use that value
        if ($this->options['shared_hosting'] !== null) {
            return (bool)$this->options['shared_hosting'];
        }
        
        // Otherwise, detect
        return Environment::isSharedHosting();
    }
    
    /**
     * Auto-detect and set recommended options based on environment.
     *
     * @return void
     */
    private function autoDetectOptions(): void
    {
        $recommended = Environment::getRecommendedConfig();
        
        // Merge recommended options, preserving existing ones
        foreach ($recommended as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            }
        }
    }
}
