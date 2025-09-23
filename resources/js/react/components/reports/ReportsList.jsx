/**
 * ReportsList Component
 * Modern, filterable list of reports with CRUD operations
 */

import React, { useState, useEffect } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { 
  Search, 
  Filter, 
  Plus, 
  RefreshCw,
  SlidersHorizontal,
  MapPin,
  Calendar,
  AlertCircle,
  CheckCircle2,
  Clock,
  XCircle,
  Download
} from 'lucide-react'
import { useReports } from '../../hooks/useReports'
import { useAuth } from '../../hooks/useAuth'
import ReportCard from './ReportCard'
import ReportFormModal from './ReportFormModal'
import ReportDetailModal from './ReportDetailModal'
import { REPORT_TYPES, URGENCY_LEVELS, REPORT_STATUS } from '../../services/reportsService'

const ReportsList = ({ 
  initialFilters = {},
  showCreateButton = true,
  showFilters = true,
  location = null // For location-based filtering
}) => {
  const { user } = useAuth()
  const {
    reports,
    loading,
    error,
    createReport,
    updateReport,
    deleteReport,
    updateFilters,
    clearFilters,
    refresh,
    pagination,
    hasFilters
  } = useReports(initialFilters)

  const [searchTerm, setSearchTerm] = useState('')
  const [showFilterPanel, setShowFilterPanel] = useState(false)
  const [showCreateModal, setShowCreateModal] = useState(false)
  const [showDetailModal, setShowDetailModal] = useState(false)
  const [selectedReport, setSelectedReport] = useState(null)
  const [sortBy, setSortBy] = useState('created_at')
  const [sortOrder, setSortOrder] = useState('desc')

  // Local filters state
  const [filters, setFilters] = useState({
    status: '',
    type: '',
    urgency: '',
    search: '',
    ...initialFilters
  })

  useEffect(() => {
    const delayedSearch = setTimeout(() => {
      updateFilters({ ...filters, search: searchTerm })
    }, 500)

    return () => clearTimeout(delayedSearch)
  }, [searchTerm, filters, updateFilters])

  const handleFilterChange = (key, value) => {
    const newFilters = { ...filters, [key]: value }
    setFilters(newFilters)
    updateFilters(newFilters)
  }

  const handleClearFilters = () => {
    setFilters({
      status: '',
      type: '',
      urgency: '',
      search: ''
    })
    setSearchTerm('')
    clearFilters()
  }

  const handleViewReport = (report) => {
    setSelectedReport(report)
    setShowDetailModal(true)
  }

  const handleEditReport = (report) => {
    // Implement edit functionality
    console.log('Edit report:', report)
  }

  const handleDeleteReport = async (report) => {
    if (window.confirm('Are you sure you want to delete this report?')) {
      const result = await deleteReport(report.id)
      if (result.success) {
        // Show success message
      }
    }
  }

  const handleCreateReport = (newReport) => {
    // Report created successfully
    refresh()
  }

  const getStatusStats = () => {
    const stats = {
      total: reports.length,
      pending: reports.filter(r => r.status === 'pending').length,
      validated: reports.filter(r => r.status === 'validated').length,
      in_progress: reports.filter(r => r.status === 'in_progress').length,
      completed: reports.filter(r => r.status === 'completed').length,
      rejected: reports.filter(r => r.status === 'rejected').length
    }
    return stats
  }

  const stats = getStatusStats()

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Reports</h2>
          <p className="text-gray-600">Environmental reports and suggestions from the community</p>
        </div>

        <div className="flex items-center space-x-3">
          <motion.button
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            onClick={refresh}
            disabled={loading}
            className="btn-secondary flex items-center space-x-2"
          >
            <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
            <span>Refresh</span>
          </motion.button>

          {showCreateButton && (
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              onClick={() => setShowCreateModal(true)}
              className="btn-primary flex items-center space-x-2"
            >
              <Plus className="w-4 h-4" />
              <span>New Report</span>
            </motion.button>
          )}
        </div>
      </div>

      {/* Statistics Cards */}
      <div className="grid grid-cols-2 md:grid-cols-6 gap-4">
        <motion.div
          whileHover={{ scale: 1.02 }}
          className="bg-white rounded-lg border border-gray-200 p-4"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Total</p>
              <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
            </div>
            <div className="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
              <MapPin className="w-4 h-4 text-gray-600" />
            </div>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ scale: 1.02 }}
          className="bg-white rounded-lg border border-gray-200 p-4"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-yellow-600">Pending</p>
              <p className="text-2xl font-bold text-yellow-900">{stats.pending}</p>
            </div>
            <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
              <Clock className="w-4 h-4 text-yellow-600" />
            </div>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ scale: 1.02 }}
          className="bg-white rounded-lg border border-gray-200 p-4"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-blue-600">Validated</p>
              <p className="text-2xl font-bold text-blue-900">{stats.validated}</p>
            </div>
            <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
              <CheckCircle2 className="w-4 h-4 text-blue-600" />
            </div>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ scale: 1.02 }}
          className="bg-white rounded-lg border border-gray-200 p-4"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-orange-600">In Progress</p>
              <p className="text-2xl font-bold text-orange-900">{stats.in_progress}</p>
            </div>
            <div className="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
              <AlertCircle className="w-4 h-4 text-orange-600" />
            </div>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ scale: 1.02 }}
          className="bg-white rounded-lg border border-gray-200 p-4"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-green-600">Completed</p>
              <p className="text-2xl font-bold text-green-900">{stats.completed}</p>
            </div>
            <div className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
              <CheckCircle2 className="w-4 h-4 text-green-600" />
            </div>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ scale: 1.02 }}
          className="bg-white rounded-lg border border-gray-200 p-4"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-red-600">Rejected</p>
              <p className="text-2xl font-bold text-red-900">{stats.rejected}</p>
            </div>
            <div className="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
              <XCircle className="w-4 h-4 text-red-600" />
            </div>
          </div>
        </motion.div>
      </div>

      {/* Filters and Search */}
      {showFilters && (
        <div className="bg-white rounded-xl border border-gray-200 p-6">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {/* Search */}
            <div className="relative flex-1 max-w-md">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input
                type="text"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                placeholder="Search reports..."
                className="input-field pl-10"
              />
            </div>

            {/* Filter Toggle */}
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              onClick={() => setShowFilterPanel(!showFilterPanel)}
              className={`flex items-center space-x-2 px-4 py-2 rounded-lg border transition-colors ${
                showFilterPanel || hasFilters
                  ? 'border-primary-300 bg-primary-50 text-primary-700'
                  : 'border-gray-300 text-gray-700 hover:bg-gray-50'
              }`}
            >
              <SlidersHorizontal className="w-4 h-4" />
              <span>Filters</span>
              {hasFilters && (
                <span className="w-2 h-2 bg-primary-500 rounded-full" />
              )}
            </motion.button>
          </div>

          {/* Filter Panel */}
          <AnimatePresence>
            {showFilterPanel && (
              <motion.div
                initial={{ height: 0, opacity: 0 }}
                animate={{ height: 'auto', opacity: 1 }}
                exit={{ height: 0, opacity: 0 }}
                className="overflow-hidden mt-6"
              >
                <div className="border-t border-gray-200 pt-6">
                  <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {/* Status Filter */}
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Status
                      </label>
                      <select
                        value={filters.status}
                        onChange={(e) => handleFilterChange('status', e.target.value)}
                        className="input-field"
                      >
                        <option value="">All Status</option>
                        {Object.entries(REPORT_STATUS).map(([key, status]) => (
                          <option key={key} value={key}>
                            {status.label}
                          </option>
                        ))}
                      </select>
                    </div>

                    {/* Type Filter */}
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Type
                      </label>
                      <select
                        value={filters.type}
                        onChange={(e) => handleFilterChange('type', e.target.value)}
                        className="input-field"
                      >
                        <option value="">All Types</option>
                        {Object.entries(REPORT_TYPES).map(([key, type]) => (
                          <option key={key} value={key}>
                            {type.label}
                          </option>
                        ))}
                      </select>
                    </div>

                    {/* Urgency Filter */}
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Urgency
                      </label>
                      <select
                        value={filters.urgency}
                        onChange={(e) => handleFilterChange('urgency', e.target.value)}
                        className="input-field"
                      >
                        <option value="">All Urgency</option>
                        {Object.entries(URGENCY_LEVELS).map(([key, level]) => (
                          <option key={key} value={key}>
                            {level.label}
                          </option>
                        ))}
                      </select>
                    </div>

                    {/* Actions */}
                    <div className="flex items-end">
                      <motion.button
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        onClick={handleClearFilters}
                        className="btn-secondary w-full"
                        disabled={!hasFilters}
                      >
                        Clear Filters
                      </motion.button>
                    </div>
                  </div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      )}

      {/* Error State */}
      {error && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700"
        >
          <div className="flex items-center space-x-2">
            <AlertCircle className="w-5 h-5" />
            <span>Error loading reports: {error}</span>
          </div>
        </motion.div>
      )}

      {/* Loading State */}
      {loading && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {[...Array(6)].map((_, index) => (
            <div key={index} className="bg-white rounded-xl border border-gray-200 p-6 animate-pulse">
              <div className="flex items-start space-x-3 mb-4">
                <div className="w-10 h-10 bg-gray-200 rounded-lg" />
                <div className="flex-1">
                  <div className="h-4 bg-gray-200 rounded mb-2" />
                  <div className="h-3 bg-gray-200 rounded w-3/4" />
                </div>
              </div>
              <div className="h-3 bg-gray-200 rounded mb-2" />
              <div className="h-3 bg-gray-200 rounded w-5/6" />
            </div>
          ))}
        </div>
      )}

      {/* Reports Grid */}
      {!loading && reports.length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <AnimatePresence>
            {reports.map((report) => (
              <ReportCard
                key={report.id}
                report={report}
                onView={handleViewReport}
                onEdit={handleEditReport}
                onDelete={handleDeleteReport}
                isOwner={user?.id === report.user_id}
                canValidate={user?.role === 'admin' || user?.role === 'moderator'}
              />
            ))}
          </AnimatePresence>
        </motion.div>
      )}

      {/* Empty State */}
      {!loading && reports.length === 0 && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="text-center py-12"
        >
          <div className="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <MapPin className="w-10 h-10 text-gray-400" />
          </div>
          <h3 className="text-lg font-medium text-gray-900 mb-2">
            {hasFilters ? 'No reports match your filters' : 'No reports yet'}
          </h3>
          <p className="text-gray-600 mb-6 max-w-md mx-auto">
            {hasFilters
              ? 'Try adjusting your search criteria or clearing the filters.'
              : 'Be the first to report an environmental issue or suggest an improvement.'}
          </p>
          {hasFilters ? (
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              onClick={handleClearFilters}
              className="btn-secondary"
            >
              Clear Filters
            </motion.button>
          ) : showCreateButton ? (
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              onClick={() => setShowCreateModal(true)}
              className="btn-primary flex items-center space-x-2 mx-auto"
            >
              <Plus className="w-4 h-4" />
              <span>Create First Report</span>
            </motion.button>
          ) : null}
        </motion.div>
      )}

      {/* Pagination */}
      {pagination && pagination.last_page > 1 && (
        <div className="flex justify-center">
          <div className="flex items-center space-x-2">
            {[...Array(pagination.last_page)].map((_, index) => {
              const page = index + 1
              return (
                <motion.button
                  key={page}
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                  onClick={() => updateFilters({ page })}
                  className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                    page === pagination.current_page
                      ? 'bg-primary-600 text-white'
                      : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  {page}
                </motion.button>
              )
            })}
          </div>
        </div>
      )}

      {/* Modals */}
      <ReportFormModal
        isOpen={showCreateModal}
        onClose={() => setShowCreateModal(false)}
        location={location}
        onSuccess={handleCreateReport}
      />

      {selectedReport && (
        <ReportDetailModal
          isOpen={showDetailModal}
          onClose={() => {
            setShowDetailModal(false)
            setSelectedReport(null)
          }}
          report={selectedReport}
          canEdit={user?.id === selectedReport.user_id}
          canValidate={user?.role === 'admin' || user?.role === 'moderator'}
        />
      )}
    </div>
  )
}

export default ReportsList