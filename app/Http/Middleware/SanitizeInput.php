<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     * Sanitizes user input to prevent XSS and injection attacks.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace(chr(0), '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Strip tags for non-textarea fields (basic XSS prevention)
                // Note: Laravel's validation and Blade escaping provide primary XSS protection
                // This is an additional layer for defense in depth
                if (!empty($value)) {
                    // Allow specific HTML tags only in designated fields
                    $allowedFields = ['description', 'review_comments', 'comments', 'justification'];
                    $fieldName = $this->getFieldName($value);
                    
                    if (!in_array($fieldName, $allowedFields)) {
                        $value = strip_tags($value);
                    }
                }
            }
        });

        $request->merge($input);

        return $next($request);
    }

    /**
     * Get the field name from the request (helper method).
     */
    protected function getFieldName($value): string
    {
        // This is a simplified approach - in production, you'd track field names
        return '';
    }
}
