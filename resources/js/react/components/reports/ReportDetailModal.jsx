/**
 * ReportDetailModal Component
 * Comprehensive modal for viewing and managing report details
 */

import React, { useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { 
  X, 
  MapPin, 
  Calendar, 
  User, 
  Edit,
  Trash2,
  CheckCircle,
  XCircle,
  Clock,
  MessageSquare,
  Image as ImageIcon,
  TreePine,
  Wrench,
  AlertTriangle,
  Leaf,
  Navigation,
  Share2,
  Download
} from 'lucide-react'
import { REPORT_TYPES, URGENCY_LEVELS, REPORT_STATUS } from '../../services/reportsService'
import { useReportValidation } from '../../hooks/useReports'

const ReportDetailModal = ({ 
  isOpen, 
  onClose, 
  report,
  canEdit = false,
  canValidate = false,
  onEdit,
  onDelete 
}) => {
  const [activeImageIndex, setActiveImageIndex] = useState(0)
  const [showValidationForm, setShowValidationForm] = useState(false)
  const [validationData, setValidationData] = useState({
    status: 'validated',
    validation_notes: ''
  })
  
  const { validateReport, validating, validationError } = useReportValidation()

  if (!report) return null

  const getTypeIcon = (type) => {
    switch (type) {
      case 'tree_planting': return TreePine
      case 'maintenance': return Wrench
      case 'pollution': return AlertTriangle
      case 'green_space_suggestion': return Leaf
      default: return Leaf
    }
  }

  const getStatusIcon = (status) => {
    switch (status) {
      case 'completed': return CheckCircle
      case 'in_progress': return Clock
      case 'validated': return CheckCircle
      case 'pending': return Clock
      case 'rejected': return XCircle
      default: return Clock
    }
  }

  const getStatusColor = (status) => {
    const colors = REPORT_STATUS[status]?.color || 'gray'
    return {
      'yellow': 'bg-yellow-100 text-yellow-800 border-yellow-200',
      'blue': 'bg-blue-100 text-blue-800 border-blue-200',
      'orange': 'bg-orange-100 text-orange-800 border-orange-200',
      'green': 'bg-green-100 text-green-800 border-green-200',
      'red': 'bg-red-100 text-red-800 border-red-200',
      'gray': 'bg-gray-100 text-gray-800 border-gray-200'
    }[colors] || 'bg-gray-100 text-gray-800 border-gray-200'
  }

  const getUrgencyColor = (urgency) => {
    const colors = URGENCY_LEVELS[urgency]?.color || 'gray'
    return {
      'green': 'text-green-600',
      'orange': 'text-orange-600', 
      'red': 'text-red-600',
      'gray': 'text-gray-600'
    }[colors] || 'text-gray-600'
  }

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const handleValidation = async (e) => {
    e.preventDefault()
    
    const result = await validateReport(report.id, validationData)
    if (result.success) {
      setShowValidationForm(false)
      onClose()
      // Refresh the reports list
      window.location.reload()
    }
  }

  const openInMaps = () => {
    const url = `https://www.google.com/maps?q=${report.latitude},${report.longitude}`
    window.open(url, '_blank')
  }

  const shareReport = () => {
    if (navigator.share) {
      navigator.share({
        title: report.title,
        text: report.description,
        url: window.location.href
      })
    } else {
      // Fallback to clipboard
      navigator.clipboard.writeText(window.location.href)
    }
  }

  const TypeIcon = getTypeIcon(report.type)
  const StatusIcon = getStatusIcon(report.status)

  return (
    <AnimatePresence>
      {isOpen && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
          onClick={(e) => e.target === e.currentTarget && onClose()}
        >
          <motion.div
            initial={{ scale: 0.9, opacity: 0, y: 20 }}
            animate={{ scale: 1, opacity: 1, y: 0 }}
            exit={{ scale: 0.9, opacity: 0, y: 20 }}
            className="bg-white rounded-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden shadow-2xl"
          >
            {/* Header */}
            <div className="bg-gradient-to-r from-primary-600 to-green-600 px-6 py-4 text-white">
              <div className="flex items-start justify-between">
                <div className="flex items-start space-x-4 flex-1">
                  <div className="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <TypeIcon className="w-6 h-6" />
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <h2 className="text-xl font-semibold mb-1 line-clamp-2">{report.title}</h2>
                    <div className="flex items-center space-x-4 text-sm text-green-100">
                      <span className="flex items-center space-x-1">
                        <span className="capitalize">{REPORT_TYPES[report.type]?.label}</span>
                      </span>
                      <span className={`capitalize font-medium ${getUrgencyColor(report.urgency)}`}>
                        {URGENCY_LEVELS[report.urgency]?.label} Priority
                      </span>
                    </div>
                  </div>
                </div>

                <div className="flex items-center space-x-2 flex-shrink-0">
                  <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border bg-white/20 border-white/30 text-white`}>
                    <StatusIcon className="w-3 h-3 mr-1" />
                    {REPORT_STATUS[report.status]?.label || report.status}
                  </span>

                  <motion.button
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.9 }}
                    onClick={onClose}
                    className="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors"
                  >
                    <X className="w-5 h-5" />
                  </motion.button>
                </div>
              </div>
            </div>

            {/* Content */}
            <div className="overflow-y-auto max-h-[calc(95vh-200px)]">
              <div className="p-6 space-y-6">
                {/* Images */}
                {report.images && report.images.length > 0 && (
                  <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                      <ImageIcon className="w-5 h-5" />
                      <span>Images ({report.images.length})</span>
                    </h3>
                    
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                      <div className="space-y-3">
                        <img
                          src={`/storage/${report.images[activeImageIndex]}`}
                          alt={`Report image ${activeImageIndex + 1}`}
                          className="w-full h-64 object-cover rounded-lg shadow-md"
                        />
                        
                        {report.images.length > 1 && (
                          <div className="flex space-x-2 overflow-x-auto">
                            {report.images.map((image, index) => (
                              <motion.button
                                key={index}
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                                onClick={() => setActiveImageIndex(index)}
                                className={`flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden ${
                                  index === activeImageIndex ? 'ring-2 ring-primary-500' : ''
                                }`}
                              >
                                <img
                                  src={`/storage/${image}`}
                                  alt={`Thumbnail ${index + 1}`}
                                  className="w-full h-full object-cover"
                                />
                              </motion.button>
                            ))}
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                )}

                {/* Description */}
                <div className="space-y-3">
                  <h3 className="text-lg font-semibold text-gray-900">Description</h3>
                  <p className="text-gray-700 leading-relaxed whitespace-pre-wrap">
                    {report.description}
                  </p>
                </div>

                {/* Details Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  {/* Location Information */}
                  <div className="space-y-3">
                    <h3 className="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                      <MapPin className="w-5 h-5" />
                      <span>Location</span>
                    </h3>
                    
                    <div className="bg-gray-50 rounded-lg p-4 space-y-3">
                      {report.address && (
                        <div>
                          <p className="text-sm font-medium text-gray-600">Address</p>
                          <p className="text-gray-900">{report.address}</p>
                        </div>
                      )}
                      
                      <div>
                        <p className="text-sm font-medium text-gray-600">Coordinates</p>
                        <p className="text-gray-900">
                          {report.latitude?.toFixed(6)}, {report.longitude?.toFixed(6)}
                        </p>
                      </div>

                      <motion.button
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                        onClick={openInMaps}
                        className="btn-secondary flex items-center space-x-2 w-full justify-center"
                      >
                        <Navigation className="w-4 h-4" />
                        <span>Open in Maps</span>
                      </motion.button>
                    </div>
                  </div>

                  {/* Report Information */}
                  <div className="space-y-3">
                    <h3 className="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                      <MessageSquare className="w-5 h-5" />
                      <span>Report Details</span>
                    </h3>
                    
                    <div className="bg-gray-50 rounded-lg p-4 space-y-3">
                      <div>
                        <p className="text-sm font-medium text-gray-600">Reported by</p>
                        <div className="flex items-center space-x-2">
                          <User className="w-4 h-4 text-gray-500" />
                          <span className="text-gray-900">{report.user?.name || 'Anonymous'}</span>
                        </div>
                      </div>

                      <div>
                        <p className="text-sm font-medium text-gray-600">Date</p>
                        <div className="flex items-center space-x-2">
                          <Calendar className="w-4 h-4 text-gray-500" />
                          <span className="text-gray-900">{formatDate(report.created_at)}</span>
                        </div>
                      </div>

                      <div>
                        <p className="text-sm font-medium text-gray-600">Priority Level</p>
                        <span className={`text-sm font-medium capitalize ${getUrgencyColor(report.urgency)}`}>
                          {URGENCY_LEVELS[report.urgency]?.label}
                        </span>
                      </div>

                      {report.validated_by && (
                        <div>
                          <p className="text-sm font-medium text-gray-600">Validated by</p>
                          <span className="text-gray-900">{report.validator?.name}</span>
                        </div>
                      )}

                      {report.validation_notes && (
                        <div>
                          <p className="text-sm font-medium text-gray-600">Validation Notes</p>
                          <p className="text-gray-900 text-sm">{report.validation_notes}</p>
                        </div>
                      )}
                    </div>
                  </div>
                </div>

                {/* Validation Form */}
                {canValidate && report.status === 'pending' && (
                  <div className="border-t border-gray-200 pt-6">
                    <div className="flex items-center justify-between mb-4">
                      <h3 className="text-lg font-semibold text-gray-900">Validation</h3>
                      <motion.button
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        onClick={() => setShowValidationForm(!showValidationForm)}
                        className="btn-primary"
                      >
                        {showValidationForm ? 'Cancel' : 'Validate Report'}
                      </motion.button>
                    </div>

                    <AnimatePresence>
                      {showValidationForm && (
                        <motion.div
                          initial={{ height: 0, opacity: 0 }}
                          animate={{ height: 'auto', opacity: 1 }}
                          exit={{ height: 0, opacity: 0 }}
                          className="overflow-hidden"
                        >
                          <form onSubmit={handleValidation} className="bg-gray-50 rounded-lg p-4 space-y-4">
                            {validationError && (
                              <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                {validationError}
                              </div>
                            )}

                            <div>
                              <label className="block text-sm font-medium text-gray-700 mb-2">
                                Validation Status
                              </label>
                              <select
                                value={validationData.status}
                                onChange={(e) => setValidationData(prev => ({ ...prev, status: e.target.value }))}
                                className="input-field"
                                required
                              >
                                <option value="validated">Approve</option>
                                <option value="rejected">Reject</option>
                                <option value="in_progress">Mark as In Progress</option>
                              </select>
                            </div>

                            <div>
                              <label className="block text-sm font-medium text-gray-700 mb-2">
                                Notes (Optional)
                              </label>
                              <textarea
                                value={validationData.validation_notes}
                                onChange={(e) => setValidationData(prev => ({ ...prev, validation_notes: e.target.value }))}
                                placeholder="Add notes about your validation decision..."
                                rows={3}
                                className="input-field"
                              />
                            </div>

                            <div className="flex space-x-3">
                              <motion.button
                                type="button"
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                                onClick={() => setShowValidationForm(false)}
                                className="btn-secondary flex-1"
                                disabled={validating}
                              >
                                Cancel
                              </motion.button>
                              <motion.button
                                type="submit"
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                                className="btn-primary flex-1 flex items-center justify-center space-x-2"
                                disabled={validating}
                              >
                                {validating ? (
                                  <>
                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                    <span>Processing...</span>
                                  </>
                                ) : (
                                  <>
                                    <CheckCircle className="w-4 h-4" />
                                    <span>Submit Validation</span>
                                  </>
                                )}
                              </motion.button>
                            </div>
                          </form>
                        </motion.div>
                      )}
                    </AnimatePresence>
                  </div>
                )}
              </div>
            </div>

            {/* Footer Actions */}
            <div className="border-t border-gray-200 px-6 py-4 bg-gray-50">
              <div className="flex items-center justify-between">
                <div className="flex space-x-3">
                  <motion.button
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    onClick={shareReport}
                    className="btn-secondary flex items-center space-x-2"
                  >
                    <Share2 className="w-4 h-4" />
                    <span>Share</span>
                  </motion.button>
                </div>

                <div className="flex space-x-3">
                  {canEdit && (
                    <>
                      <motion.button
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        onClick={() => onEdit?.(report)}
                        className="btn-secondary flex items-center space-x-2"
                      >
                        <Edit className="w-4 h-4" />
                        <span>Edit</span>
                      </motion.button>

                      <motion.button
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        onClick={() => onDelete?.(report)}
                        className="btn-secondary text-red-600 border-red-200 hover:bg-red-50 flex items-center space-x-2"
                      >
                        <Trash2 className="w-4 h-4" />
                        <span>Delete</span>
                      </motion.button>
                    </>
                  )}

                  <motion.button
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    onClick={onClose}
                    className="btn-secondary"
                  >
                    Close
                  </motion.button>
                </div>
              </div>
            </div>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  )
}

export default ReportDetailModal