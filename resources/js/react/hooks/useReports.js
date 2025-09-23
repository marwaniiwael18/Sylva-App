import { useState, useEffect, useCallback } from 'react'
import { ReportsService } from '../services/reportsService'
import { useApi } from './useApi'

/**
 * Custom hook for managing reports
 * Provides all necessary functions and state for report management
 */
export const useReports = (initialFilters = {}) => {
  const [reports, setReports] = useState([])
  const [pagination, setPagination] = useState(null)
  const [filters, setFilters] = useState(initialFilters)
  const [selectedReport, setSelectedReport] = useState(null)
  
  const {
    loading,
    error,
    execute: fetchReports
  } = useApi(ReportsService.getAllReports)

  const {
    loading: creating,
    error: createError,
    execute: executeCreate
  } = useApi(ReportsService.createReport)

  const {
    loading: updating,
    error: updateError,
    execute: executeUpdate
  } = useApi(ReportsService.updateReport)

  const {
    loading: deleting,
    error: deleteError,
    execute: executeDelete
  } = useApi(ReportsService.deleteReport)

  // Load reports when component mounts or filters change
  useEffect(() => {
    loadReports()
  }, [filters])

  /**
   * Load reports with current filters
   */
  const loadReports = useCallback(async () => {
    const result = await fetchReports(filters)
    if (result.success) {
      setReports(result.data)
      setPagination(result.pagination)
    }
  }, [filters, fetchReports])

  /**
   * Create a new report
   */
  const createReport = useCallback(async (reportData) => {
    const result = await executeCreate(reportData)
    if (result.success) {
      // Add new report to the beginning of the list
      setReports(prev => [result.data, ...prev])
      return result
    }
    return result
  }, [executeCreate])

  /**
   * Update an existing report
   */
  const updateReport = useCallback(async (id, reportData) => {
    const result = await executeUpdate(id, reportData)
    if (result.success) {
      // Update the report in the list
      setReports(prev => prev.map(report => 
        report.id === id ? result.data : report
      ))
      // Update selected report if it's the one being updated
      if (selectedReport?.id === id) {
        setSelectedReport(result.data)
      }
      return result
    }
    return result
  }, [executeUpdate, selectedReport])

  /**
   * Delete a report
   */
  const deleteReport = useCallback(async (id) => {
    const result = await executeDelete(id)
    if (result.success) {
      // Remove report from the list
      setReports(prev => prev.filter(report => report.id !== id))
      // Clear selected report if it's the one being deleted
      if (selectedReport?.id === id) {
        setSelectedReport(null)
      }
      return result
    }
    return result
  }, [executeDelete, selectedReport])

  /**
   * Update filters
   */
  const updateFilters = useCallback((newFilters) => {
    setFilters(prev => ({ ...prev, ...newFilters }))
  }, [])

  /**
   * Clear all filters
   */
  const clearFilters = useCallback(() => {
    setFilters({})
  }, [])

  /**
   * Search reports
   */
  const searchReports = useCallback((searchTerm) => {
    updateFilters({ search: searchTerm })
  }, [updateFilters])

  /**
   * Filter by status
   */
  const filterByStatus = useCallback((status) => {
    updateFilters({ status })
  }, [updateFilters])

  /**
   * Filter by type
   */
  const filterByType = useCallback((type) => {
    updateFilters({ type })
  }, [updateFilters])

  /**
   * Filter by urgency
   */
  const filterByUrgency = useCallback((urgency) => {
    updateFilters({ urgency })
  }, [updateFilters])

  /**
   * Filter by location
   */
  const filterByLocation = useCallback((latitude, longitude, radius = 10) => {
    updateFilters({ latitude, longitude, radius })
  }, [updateFilters])

  /**
   * Get reports near a location
   */
  const getReportsNear = useCallback(async (latitude, longitude, radius = 10) => {
    const result = await ReportsService.getReportsNear(latitude, longitude, radius)
    return result
  }, [])

  /**
   * Refresh reports list
   */
  const refresh = useCallback(() => {
    loadReports()
  }, [loadReports])

  /**
   * Select a report
   */
  const selectReport = useCallback((report) => {
    setSelectedReport(report)
  }, [])

  /**
   * Clear selected report
   */
  const clearSelection = useCallback(() => {
    setSelectedReport(null)
  }, [])

  return {
    // State
    reports,
    pagination,
    filters,
    selectedReport,
    
    // Loading states
    loading,
    creating,
    updating,
    deleting,
    
    // Errors
    error,
    createError,
    updateError,
    deleteError,
    
    // Actions
    loadReports,
    createReport,
    updateReport,
    deleteReport,
    refresh,
    
    // Filtering
    updateFilters,
    clearFilters,
    searchReports,
    filterByStatus,
    filterByType,
    filterByUrgency,
    filterByLocation,
    getReportsNear,
    
    // Selection
    selectReport,
    clearSelection,
    
    // Utilities
    hasReports: reports.length > 0,
    isEmpty: reports.length === 0 && !loading,
    hasFilters: Object.keys(filters).length > 0
  }
}

/**
 * Hook for managing report validation (admin/moderator only)
 */
export const useReportValidation = () => {
  const {
    loading: validating,
    error: validationError,
    execute: executeValidation
  } = useApi(ReportsService.validateReport)

  const validateReport = useCallback(async (id, validationData) => {
    const result = await executeValidation(id, validationData)
    return result
  }, [executeValidation])

  return {
    validating,
    validationError,
    validateReport
  }
}

/**
 * Hook for reports statistics
 */
export const useReportsStatistics = () => {
  const [statistics, setStatistics] = useState(null)
  
  const {
    loading,
    error,
    execute: fetchStatistics
  } = useApi(ReportsService.getStatistics)

  useEffect(() => {
    loadStatistics()
  }, [])

  const loadStatistics = useCallback(async () => {
    const result = await fetchStatistics()
    if (result.success) {
      setStatistics(result.data)
    }
  }, [fetchStatistics])

  const refresh = useCallback(() => {
    loadStatistics()
  }, [loadStatistics])

  return {
    statistics,
    loading,
    error,
    refresh
  }
}

export default useReports