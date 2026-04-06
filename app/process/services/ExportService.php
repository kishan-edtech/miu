<?php

require_once "BaseService.php";
require_once __DIR__ . '/../traits/ExportTrait.php';

class ExportService extends BaseService
{
    use ExportTrait;
    
    public function __construct($db = null, $logger = null)
    {
        // If no db passed, try to get from parent or global
        parent::__construct($db, $logger);
        
        // Set the PDO connection in the trait
        $this->setDBConnection($this->db);
    }
    
    /**
     * Export data based on payload
     */
    public function export($uni_id, $filters, $payload)
    {
        try {
            // Validate payload
            $payload = json_decode($payload,true);
            if (empty($payload) || !is_array($payload)) {
                return $this->error('Payload is not available or not valid');
            }
            
            // Check if method is specified
            if (!isset($payload['method'])) {
                return $this->error('Method not specified in payload');
            }
            
            // Handle different export methods
            switch ($payload['method']) {
                case 'students':
                    unset($payload['method']);
                    $data = $this->studentExport($uni_id, $payload, $filters);
                    
                    // Return success response with data
                    return $this->success([
                        'headers' => $data[0] ?? [],
                        'rows' => array_slice($data, 1)
                    ], 'Students exported successfully');
                    
                default:
                    return $this->error('Invalid export method: ' . $payload['method']);
            }
            
        } catch (Exception $e) {
            // Log the error
            $this->logger->error('Export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response
            return $this->error('Export failed: ' . $e->getMessage());
        }
    }
}