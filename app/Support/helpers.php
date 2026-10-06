<?php
// Global view helpers shared across portals.

if (!function_exists('edp_short')) {
    /**
     * Display form of a student EDP number: lists show only the 6-digit
     * number without the year prefix (2026-099001 -> 099001).
     */
    function edp_short(?string $studentId): string {
        if (!$studentId) {
            return '—';
        }

        return preg_replace('/^\d{4}-/', '', $studentId);
    }
}
