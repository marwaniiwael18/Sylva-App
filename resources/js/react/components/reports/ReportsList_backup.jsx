/**
 * ReportsList Component
 * Modern, filterable list of reports with CRUD operations
 */

import React, { useState, useEffect } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { 
  Search, 
  Filter,
  SlidersHorizontal,
  MapPin,
  Calendar,
  AlertCircle,
  CheckCircle,
  CheckCircle2,
  Clock,
  XCircle,
  Download,
  X,
  Plus
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
      handleFilterChange('search', searchTerm)
    }, 300) // Reduced delay for faster search

    return () => clearTimeout(delayedSearch)
  }, [searchTerm])

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

  const [showEditModal, setShowEditModal] = useState(false)
  const [editingReport, setEditingReport] = useState(null)

  const handleEditReport = (report) => {
    setEditingReport(report)
    setShowEditModal(true)
  }

  const handleUpdateSuccess = (updatedReport) => {
    setShowEditModal(false)
    setEditingReport(null)
    refresh() // Refresh the list to show updated data
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

        {showCreateButton && (
          <motion.button
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            onClick={() => setShowCreateModal(true)}
            className="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-green-200 flex items-center space-x-2 transition-all duration-200"
          >
            <Plus className="w-5 h-5" />
            <span>New Report</span>
          </motion.button>
        )}
      </div>

      {/* Enhanced Statistics Cards */}
      <div className="grid grid-cols-2 md:grid-cols-6 gap-4">
        <motion.button
          whileHover={{ scale: 1.02, y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={handleClearFilters}
          className={`w-full text-left rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200 ${
            !hasFilters 
              ? 'bg-gradient-to-br from-gray-100 to-gray-200 border-gray-300' 
              : 'bg-gradient-to-br from-white to-gray-50 border-gray-200'
          }`}
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total</p>
              <p className="text-3xl font-bold text-gray-900 mt-1">{stats.total}</p>
            </div>
            <div className="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
              <MapPin className="w-6 h-6 text-gray-600" />
            </div>
          </div>
        </motion.button>

        <motion.button
          whileHover={{ scale: 1.02, y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={() => setFilters(prev => ({...prev, status: 'pending'}))}
          className={`w-full text-left rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200 ${
            filters.status === 'pending'
              ? 'bg-gradient-to-br from-orange-100 to-amber-100 border-orange-300' 
              : 'bg-gradient-to-br from-orange-50 to-amber-50 border-orange-200'
          }`}
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-semibold text-orange-700 uppercase tracking-wide">Pending</p>
              <p className="text-3xl font-bold text-orange-800 mt-1">{stats.pending}</p>
            </div>
            <div className="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
              <AlertCircle className="w-6 h-6 text-orange-600" />
            </div>
          </div>
        </motion.button>

        <motion.button
          whileHover={{ scale: 1.02, y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={() => setFilters(prev => ({...prev, status: 'validated'}))}
          className={`w-full text-left rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200 ${
            filters.status === 'validated'
              ? 'bg-gradient-to-br from-green-100 to-emerald-100 border-green-300' 
              : 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-200'
          }`}
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-semibold text-green-700 uppercase tracking-wide">Validated</p>
              <p className="text-3xl font-bold text-green-800 mt-1">{stats.validated}</p>
            </div>
            <div className="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
              <CheckCircle className="w-6 h-6 text-green-600" />
            </div>
          </div>
        </motion.button>

        <motion.button
          whileHover={{ scale: 1.02, y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={() => setFilters(prev => ({...prev, status: 'in_progress'}))}
          className={`w-full text-left rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200 ${
            filters.status === 'in_progress'
              ? 'bg-gradient-to-br from-blue-100 to-sky-100 border-blue-300' 
              : 'bg-gradient-to-br from-blue-50 to-sky-50 border-blue-200'
          }`}
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-semibold text-blue-700 uppercase tracking-wide">In Progress</p>
              <p className="text-3xl font-bold text-blue-800 mt-1">{stats.in_progress}</p>
            </div>
            <div className="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
              <Clock className="w-6 h-6 text-blue-600" />
            </div>
          </div>
        </motion.button>

        <motion.button
          whileHover={{ scale: 1.02, y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={() => setFilters(prev => ({...prev, status: 'completed'}))}
          className={`w-full text-left rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200 ${
            filters.status === 'completed'
              ? 'bg-gradient-to-br from-emerald-100 to-teal-100 border-emerald-300' 
              : 'bg-gradient-to-br from-emerald-50 to-teal-50 border-emerald-200'
          }`}
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Completed</p>
              <p className="text-3xl font-bold text-emerald-800 mt-1">{stats.completed}</p>
            </div>
            <div className="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
              <CheckCircle2 className="w-6 h-6 text-emerald-600" />
            </div>
          </div>
        </motion.button>

        <motion.button
          whileHover={{ scale: 1.02, y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={() => setFilters(prev => ({...prev, status: 'rejected'}))}
          className={`w-full text-left rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200 ${
            filters.status === 'rejected'
              ? 'bg-gradient-to-br from-red-100 to-rose-100 border-red-300' 
              : 'bg-gradient-to-br from-red-50 to-rose-50 border-red-200'
          }`}
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-semibold text-red-700 uppercase tracking-wide">Rejected</p>
              <p className="text-3xl font-bold text-red-800 mt-1">{stats.rejected}</p>
            </div>
            <div className="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
              <XCircle className="w-6 h-6 text-red-600" />
            </div>
          </div>
        </motion.button>
      </div>

      {/* Enhanced Search & Filter Bar */}
      <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div className="flex flex-col lg:flex-row gap-4">
          {/* Search Input */}
          <div className="flex-1">
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Search className="h-5 w-5 text-gray-400" />
              </div>
              <input
                type="text"
                placeholder="Search reports by title, description, location..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
              />
              {searchTerm && (
                <button
                  onClick={() => setSearchTerm('')}
                  className="absolute inset-y-0 right-0 pr-3 flex items-center"
                >
                  <X className="h-5 w-5 text-gray-400 hover:text-gray-600" />
                </button>
              )}
            </div>
          </div>

          {/* Filter Toggle */}
          {showFilters && (
            <motion.button
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
              onClick={() => setShowFilterPanel(!showFilterPanel)}
              className={`flex items-center space-x-2 px-4 py-3 rounded-lg border transition-all duration-200 ${
                showFilterPanel
                  ? 'bg-green-50 border-green-200 text-green-700'
                  : 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100'
              }`}
            >
              <SlidersHorizontal className="w-5 h-5" />
              <span className="font-medium">Filters</span>
              {hasFilters && (
                <span className="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                  Active
                </span>
              )}
            </motion.button>
          )}
        </div>

        {/* Filter Panel */}
        <AnimatePresence>
          {showFilterPanel && showFilters && (
            <motion.div
              initial={{ opacity: 0, height: 0 }}
              animate={{ opacity: 1, height: 'auto' }}
              exit={{ opacity: 0, height: 0 }}
              transition={{ duration: 0.2 }}
              className="mt-4 pt-4 border-t border-gray-200"
            >
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* Status Filter */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Status
                  </label>
                  <select
                    value={filters.status}
                    onChange={(e) => handleFilterChange('status', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                    <option value="">All Status</option>
                    {Object.entries(REPORT_STATUS).map(([key, value]) => (
                      <option key={key} value={key}>
                        {value}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Type Filter */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Report Type
                  </label>
                  <select
                    value={filters.type}
                    onChange={(e) => handleFilterChange('type', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                    <option value="">All Types</option>
                    {Object.entries(REPORT_TYPES).map(([key, value]) => (
                      <option key={key} value={key}>
                        {value}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Urgency Filter */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Urgency Level
                  </label>
                  <select
                    value={filters.urgency}
                    onChange={(e) => handleFilterChange('urgency', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                    <option value="">All Urgency</option>
                    {Object.entries(URGENCY_LEVELS).map(([key, value]) => (
                      <option key={key} value={key}>
                        {value}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              {/* Clear Filters Button */}
              {hasFilters && (
                <div className="mt-4 flex justify-end">
                  <motion.button
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    onClick={handleClearFilters}
                    className="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                  >
                    Clear All Filters
                  </motion.button>
                </div>
              )}
            </motion.div>
          )}
        </AnimatePresence>
      </div>

      {/* Loading State */}
      {loading && (
        <div className="flex justify-center items-center py-12">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
        </div>
      )}

      {/* Error State */}
      {error && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="bg-red-50 border border-red-200 rounded-xl p-6 text-center"
        >
          <AlertCircle className="mx-auto h-12 w-12 text-red-400" />
          <h3 className="mt-2 text-sm font-medium text-red-800">Error loading reports</h3>
          <p className="mt-1 text-sm text-red-600">{error}</p>
          <button
            onClick={refresh}
            className="mt-4 bg-red-100 hover:bg-red-200 text-red-800 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
          >
            Try Again
          </button>
        </motion.div>
      )}

      {/* Reports Grid */}
      {!loading && !error && (
        <>
          {reports.length === 0 ? (
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="text-center py-12 bg-white rounded-xl border border-gray-200"
            >
              <MapPin className="mx-auto h-12 w-12 text-gray-400" />
              <h3 className="mt-2 text-sm font-medium text-gray-900">No reports found</h3>
              <p className="mt-1 text-sm text-gray-500">
                {hasFilters ? 'Try adjusting your filters' : 'Get started by creating your first report'}
              </p>
              {showCreateButton && !hasFilters && (
                <motion.button
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                  onClick={() => setShowCreateModal(true)}
                  className="mt-6 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors duration-200"
                >
                  Create Report
                </motion.button>
              )}
            </motion.div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <AnimatePresence>
                {reports.map((report, index) => (
                  <motion.div
                    key={report.id}
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -20 }}
                    transition={{ delay: index * 0.05 }}
                  >
                    <ReportCard
                      report={report}
                      onView={handleViewReport}
                      onEdit={handleEditReport}
                      onDelete={handleDeleteReport}
                      showActions={true}
                    />
                  </motion.div>
                ))}
              </AnimatePresence>
            </div>
          )}
        </>
      )}

      {/* Modals */}
      <AnimatePresence>
        {showCreateModal && (
          <ReportFormModal
            isOpen={showCreateModal}
            onClose={() => setShowCreateModal(false)}
            onSubmit={handleCreateReport}
            location={location}
          />
        )}

        {showEditModal && editingReport && (
          <ReportFormModal
            isOpen={showEditModal}
            onClose={() => {
              setShowEditModal(false)
              setEditingReport(null)
            }}
            onSubmit={handleUpdateSuccess}
            report={editingReport}
            location={location}
          />
        )}

        {showDetailModal && selectedReport && (
          <ReportDetailModal
            isOpen={showDetailModal}
            onClose={() => setShowDetailModal(false)}
            report={selectedReport}
            onEdit={handleEditReport}
            onDelete={handleDeleteReport}
          />
        )}
      </AnimatePresence>
    </div>
  )
}

export default ReportsList