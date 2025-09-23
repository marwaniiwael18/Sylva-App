/**
 * ReportCard Component
 * Modern card component for displaying individual reports
 */

import React, { useState } from 'react'
import { motion } from 'framer-motion'
import { 
  MapPin, 
  Calendar, 
  User, 
  Eye,
  Edit,
  Trash2,
  CheckCircle,
  Clock,
  AlertTriangle,
  TreePine,
  Wrench,
  Leaf,
  MoreVertical
} from 'lucide-react'
import { REPORT_TYPES, URGENCY_LEVELS, REPORT_STATUS } from '../../services/reportsService'

const ReportCard = ({ 
  report, 
  onView, 
  onEdit, 
  onDelete, 
  onValidate,
  showActions = true,
  isOwner = false,
  canValidate = false 
}) => {
  const [showDropdown, setShowDropdown] = useState(false)

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
      case 'rejected': return AlertTriangle
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
      'green': 'bg-green-500',
      'orange': 'bg-orange-500',
      'red': 'bg-red-500',
      'gray': 'bg-gray-500'
    }[colors] || 'bg-gray-500'
  }

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }

  const TypeIcon = getTypeIcon(report.type)
  const StatusIcon = getStatusIcon(report.status)

  return (
    <motion.div
      layout
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -20 }}
      whileHover={{ y: -2 }}
      className="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300 overflow-hidden"
    >
      {/* Header */}
      <div className="p-4 border-b border-gray-100">
        <div className="flex items-start justify-between">
          <div className="flex items-start space-x-3 flex-1">
            <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${
              report.urgency === 'high' ? 'bg-red-100' :
              report.urgency === 'medium' ? 'bg-orange-100' : 'bg-green-100'
            }`}>
              <TypeIcon className={`w-5 h-5 ${
                report.urgency === 'high' ? 'text-red-600' :
                report.urgency === 'medium' ? 'text-orange-600' : 'text-green-600'
              }`} />
            </div>
            
            <div className="flex-1 min-w-0">
              <h3 className="font-semibold text-gray-900 mb-1 line-clamp-2">
                {report.title}
              </h3>
              
              <div className="flex items-center space-x-4 text-sm text-gray-600">
                <div className="flex items-center space-x-1">
                  <div className={`w-2 h-2 rounded-full ${getUrgencyColor(report.urgency)}`} />
                  <span className="capitalize">{report.urgency}</span>
                </div>
                
                <div className="flex items-center space-x-1">
                  <Calendar className="w-3 h-3" />
                  <span>{formatDate(report.created_at)}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Status Badge */}
          <div className="flex items-center space-x-2">
            <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border ${getStatusColor(report.status)}`}>
              <StatusIcon className="w-3 h-3 mr-1" />
              {REPORT_STATUS[report.status]?.label || report.status}
            </span>

            {showActions && (
              <div className="relative">
                <motion.button
                  whileHover={{ scale: 1.1 }}
                  whileTap={{ scale: 0.9 }}
                  onClick={() => setShowDropdown(!showDropdown)}
                  className="p-1 rounded-lg hover:bg-gray-100 transition-colors"
                >
                  <MoreVertical className="w-4 h-4 text-gray-500" />
                </motion.button>

                {showDropdown && (
                  <div className="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10">
                    <button
                      onClick={() => {
                        onView?.(report)
                        setShowDropdown(false)
                      }}
                      className="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center space-x-2"
                    >
                      <Eye className="w-4 h-4" />
                      <span>View Details</span>
                    </button>
                    
                    {isOwner && (
                      <>
                        <button
                          onClick={() => {
                            onEdit?.(report)
                            setShowDropdown(false)
                          }}
                          className="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center space-x-2"
                        >
                          <Edit className="w-4 h-4" />
                          <span>Edit</span>
                        </button>
                        
                        <button
                          onClick={() => {
                            onDelete?.(report)
                            setShowDropdown(false)
                          }}
                          className="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center space-x-2"
                        >
                          <Trash2 className="w-4 h-4" />
                          <span>Delete</span>
                        </button>
                      </>
                    )}

                    {canValidate && report.status === 'pending' && (
                      <button
                        onClick={() => {
                          onValidate?.(report)
                          setShowDropdown(false)
                        }}
                        className="w-full px-4 py-2 text-left text-sm text-blue-600 hover:bg-blue-50 flex items-center space-x-2"
                      >
                        <CheckCircle className="w-4 h-4" />
                        <span>Validate</span>
                      </button>
                    )}
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Content */}
      <div className="p-4">
        <p className="text-gray-600 text-sm mb-3 line-clamp-3">
          {report.description}
        </p>

        {/* Images Preview */}
        {report.image_urls && report.image_urls.length > 0 && (
          <div className="mb-3">
            <div className="flex space-x-2 overflow-x-auto">
              {report.image_urls.slice(0, 3).map((imageUrl, index) => (
                <img
                  key={index}
                  src={imageUrl}
                  alt={`Report image ${index + 1}`}
                  className="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                />
              ))}
              {report.image_urls.length > 3 && (
                <div className="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                  <span className="text-xs text-gray-500">+{report.image_urls.length - 3}</span>
                </div>
              )}
            </div>
          </div>
        )}

        {/* Footer */}
        <div className="flex items-center justify-between text-sm">
          <div className="flex items-center space-x-4 text-gray-500">
            <div className="flex items-center space-x-1">
              <MapPin className="w-3 h-3" />
              <span>
                {report.address || `${parseFloat(report.latitude)?.toFixed(4)}, ${parseFloat(report.longitude)?.toFixed(4)}`}
              </span>
            </div>
            
            {report.user && (
              <div className="flex items-center space-x-1">
                <User className="w-3 h-3" />
                <span>{report.user.name}</span>
              </div>
            )}
          </div>

          <span className="text-xs text-gray-400">
            {REPORT_TYPES[report.type]?.label || report.type}
          </span>
        </div>
      </div>

      {/* Click overlay for mobile */}
      <div 
        className="absolute inset-0 md:hidden"
        onClick={() => onView?.(report)}
      />
    </motion.div>
  )
}

export default ReportCard