/**
 * ReportFormModal Component
 * Modern, feature-rich modal for submitting environmental reports
 */

import React, { useState, useEffect } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { 
  X, 
  Send, 
  MapPin, 
  Camera, 
  Upload,
  TreePine,
  Wrench,
  AlertTriangle,
  Leaf,
  Info
} from 'lucide-react'
import { useReports } from '../../hooks/useReports'
import { REPORT_TYPES, URGENCY_LEVELS } from '../../services/reportsService'

const ReportFormModal = ({ 
  isOpen, 
  onClose, 
  location, // { latitude, longitude, address? }
  existingReport, // For editing mode
  isEditing = false,
  onSuccess 
}) => {
  const { createReport, updateReport, creating, updating, createError, updateError } = useReports()
  
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    type: 'green_space_suggestion',
    urgency: 'low',
    images: [],
    address: location?.address || ''
  })
  
  const [errors, setErrors] = useState({})
  const [dragActive, setDragActive] = useState(false)
  const [imagePreview, setImagePreview] = useState([])

  // Reset form when modal opens/closes or populate for editing
  useEffect(() => {
    if (isOpen) {
      if (isEditing && existingReport) {
        setFormData({
          title: existingReport.title || '',
          description: existingReport.description || '',
          type: existingReport.type || 'green_space_suggestion',
          urgency: existingReport.urgency || 'low',
          images: [],
          address: existingReport.address || ''
        })
      } else {
        setFormData({
          title: '',
          description: '',
          type: 'green_space_suggestion',
          urgency: 'low',
          images: [],
          address: location?.address || ''
        })
      }
      setErrors({})
      setImagePreview([])
    }
  }, [isOpen, location, isEditing, existingReport])

  const handleInputChange = (e) => {
    const { name, value } = e.target
    setFormData(prev => ({ ...prev, [name]: value }))
    // Clear error when user starts typing
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }

  const handleImageUpload = (files) => {
    const validFiles = Array.from(files).filter(file => {
      if (!file.type.startsWith('image/')) return false
      if (file.size > 2048000) return false // 2MB limit
      return true
    })

    if (formData.images.length + validFiles.length > 5) {
      setErrors(prev => ({ ...prev, images: 'Maximum 5 images allowed' }))
      return
    }

    setFormData(prev => ({
      ...prev,
      images: [...prev.images, ...validFiles]
    }))

    // Generate preview URLs
    validFiles.forEach(file => {
      const reader = new FileReader()
      reader.onload = (e) => {
        setImagePreview(prev => [...prev, {
          file,
          url: e.target.result,
          id: Date.now() + Math.random()
        }])
      }
      reader.readAsDataURL(file)
    })

    // Clear image errors
    if (errors.images) {
      setErrors(prev => ({ ...prev, images: '' }))
    }
  }

  const removeImage = (index) => {
    setFormData(prev => ({
      ...prev,
      images: prev.images.filter((_, i) => i !== index)
    }))
    setImagePreview(prev => prev.filter((_, i) => i !== index))
  }

  const handleDrag = (e) => {
    e.preventDefault()
    e.stopPropagation()
    if (e.type === 'dragenter' || e.type === 'dragover') {
      setDragActive(true)
    } else if (e.type === 'dragleave') {
      setDragActive(false)
    }
  }

  const handleDrop = (e) => {
    e.preventDefault()
    e.stopPropagation()
    setDragActive(false)
    
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleImageUpload(e.dataTransfer.files)
    }
  }

  const validateForm = () => {
    const newErrors = {}

    if (!formData.title.trim()) {
      newErrors.title = 'Title is required'
    } else if (formData.title.trim().length < 5) {
      newErrors.title = 'Title must be at least 5 characters'
    }

    if (!formData.description.trim()) {
      newErrors.description = 'Description is required'
    } else if (formData.description.trim().length < 20) {
      newErrors.description = 'Please provide more details (at least 20 characters)'
    }

    if (!formData.type) {
      newErrors.type = 'Please select a report type'
    }

    if (!formData.urgency) {
      newErrors.urgency = 'Please select urgency level'
    }

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    
    if (!validateForm()) return

    const reportData = {
      ...formData,
      latitude: isEditing ? existingReport.latitude : location.latitude,
      longitude: isEditing ? existingReport.longitude : location.longitude
    }

    let result
    if (isEditing) {
      result = await updateReport(existingReport.id, reportData)
    } else {
      result = await createReport(reportData)
    }
    
    if (result.success) {
      onSuccess?.(result.data)
      onClose()
    } else {
      setErrors(prev => ({
        ...prev,
        submit: result.message || `Failed to ${isEditing ? 'update' : 'submit'} report`
      }))
    }
  }

  const getTypeIcon = (type) => {
    switch (type) {
      case 'tree_planting': return TreePine
      case 'maintenance': return Wrench
      case 'pollution': return AlertTriangle
      case 'green_space_suggestion': return Leaf
      default: return Leaf
    }
  }

  const getUrgencyColor = (level) => {
    switch (level) {
      case 'high': return 'text-red-600 border-red-200 bg-red-50'
      case 'medium': return 'text-orange-600 border-orange-200 bg-orange-50'
      case 'low': return 'text-green-600 border-green-200 bg-green-50'
      default: return 'text-gray-600 border-gray-200 bg-gray-50'
    }
  }

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
            className="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl"
          >
            {/* Header */}
            <div className="bg-gradient-to-r from-primary-600 to-green-600 px-6 py-4 text-white">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <MapPin className="w-5 h-5" />
                  </div>
                  <div>
                    <h2 className="text-xl font-semibold">{isEditing ? 'Edit Report' : 'Submit Report'}</h2>
                    <p className="text-sm text-green-100">Help improve your community</p>
                  </div>
                </div>
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

            {/* Form Content */}
            <div className="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
              <form onSubmit={handleSubmit} className="space-y-6">
                {/* Error Display */}
                {(errors.submit || createError) && (
                  <motion.div
                    initial={{ opacity: 0, y: -10 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center space-x-2"
                  >
                    <AlertTriangle className="w-5 h-5 flex-shrink-0" />
                    <span>{errors.submit || createError}</span>
                  </motion.div>
                )}

                {/* Location Info */}
                <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div className="flex items-center space-x-2 mb-2">
                    <MapPin className="w-4 h-4 text-blue-600" />
                    <span className="text-sm font-medium text-blue-900">Report Location</span>
                  </div>
                  <p className="text-sm text-blue-700">
                    {formData.address || `Lat: ${location?.latitude?.toFixed(6)}, Lng: ${location?.longitude?.toFixed(6)}`}
                  </p>
                </div>

                {/* Report Type */}
                <div className="space-y-3">
                  <label className="block text-sm font-medium text-gray-700">
                    Report Type <span className="text-red-500">*</span>
                  </label>
                  <div className="grid grid-cols-2 gap-3">
                    {Object.entries(REPORT_TYPES).map(([key, type]) => {
                      const Icon = getTypeIcon(key)
                      return (
                        <motion.label
                          key={key}
                          whileHover={{ scale: 1.02 }}
                          whileTap={{ scale: 0.98 }}
                          className={`relative flex items-center space-x-3 p-4 border rounded-xl cursor-pointer transition-all ${
                            formData.type === key
                              ? 'border-primary-300 bg-primary-50 shadow-md'
                              : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                          }`}
                        >
                          <input
                            type="radio"
                            name="type"
                            value={key}
                            checked={formData.type === key}
                            onChange={handleInputChange}
                            className="sr-only"
                          />
                          <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${
                            formData.type === key ? 'bg-primary-100' : 'bg-gray-100'
                          }`}>
                            <Icon className={`w-5 h-5 ${
                              formData.type === key ? 'text-primary-600' : 'text-gray-600'
                            }`} />
                          </div>
                          <div>
                            <p className={`font-medium ${
                              formData.type === key ? 'text-primary-900' : 'text-gray-900'
                            }`}>
                              {type.label}
                            </p>
                            <p className="text-xs text-gray-500">{type.description}</p>
                          </div>
                        </motion.label>
                      )
                    })}
                  </div>
                  {errors.type && <p className="text-red-600 text-sm">{errors.type}</p>}
                </div>

                {/* Title */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Title <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="title"
                    value={formData.title}
                    onChange={handleInputChange}
                    placeholder="Brief description of the issue or suggestion"
                    className={`input-field ${errors.title ? 'border-red-300 focus:border-red-500' : ''}`}
                    maxLength={255}
                  />
                  <div className="flex justify-between mt-1">
                    {errors.title && <p className="text-red-600 text-sm">{errors.title}</p>}
                    <p className="text-xs text-gray-500 ml-auto">
                      {formData.title.length}/255
                    </p>
                  </div>
                </div>

                {/* Description */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Description <span className="text-red-500">*</span>
                  </label>
                  <textarea
                    name="description"
                    value={formData.description}
                    onChange={handleInputChange}
                    placeholder="Provide detailed information about the issue, location context, urgency reasons, or suggestions for improvement..."
                    rows={4}
                    className={`input-field ${errors.description ? 'border-red-300 focus:border-red-500' : ''}`}
                    maxLength={1000}
                  />
                  <div className="flex justify-between mt-1">
                    {errors.description && <p className="text-red-600 text-sm">{errors.description}</p>}
                    <p className="text-xs text-gray-500 ml-auto">
                      {formData.description.length}/1000
                    </p>
                  </div>
                </div>

                {/* Urgency Level */}
                <div className="space-y-3">
                  <label className="block text-sm font-medium text-gray-700">
                    Urgency Level <span className="text-red-500">*</span>
                  </label>
                  <div className="grid grid-cols-3 gap-3">
                    {Object.entries(URGENCY_LEVELS).map(([key, level]) => (
                      <motion.label
                        key={key}
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                        className={`relative flex flex-col items-center p-4 border rounded-xl cursor-pointer transition-all ${
                          formData.urgency === key
                            ? `border-${level.color}-300 bg-${level.color}-50 shadow-md`
                            : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                        }`}
                      >
                        <input
                          type="radio"
                          name="urgency"
                          value={key}
                          checked={formData.urgency === key}
                          onChange={handleInputChange}
                          className="sr-only"
                        />
                        <div className={`w-8 h-8 rounded-full mb-2 flex items-center justify-center ${getUrgencyColor(key)}`}>
                          <div className={`w-3 h-3 rounded-full bg-current`}></div>
                        </div>
                        <p className={`font-medium capitalize ${
                          formData.urgency === key ? `text-${level.color}-900` : 'text-gray-900'
                        }`}>
                          {level.label}
                        </p>
                      </motion.label>
                    ))}
                  </div>
                  {errors.urgency && <p className="text-red-600 text-sm">{errors.urgency}</p>}
                </div>

                {/* Image Upload */}
                <div className="space-y-3">
                  <label className="block text-sm font-medium text-gray-700">
                    Images (Optional)
                  </label>
                  <div
                    className={`relative border-2 border-dashed rounded-xl p-6 text-center transition-all ${
                      dragActive
                        ? 'border-primary-400 bg-primary-50'
                        : 'border-gray-300 hover:border-gray-400'
                    }`}
                    onDragEnter={handleDrag}
                    onDragLeave={handleDrag}
                    onDragOver={handleDrag}
                    onDrop={handleDrop}
                  >
                    <input
                      type="file"
                      multiple
                      accept="image/*"
                      onChange={(e) => handleImageUpload(e.target.files)}
                      className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    />
                    <div className="flex flex-col items-center space-y-2">
                      <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <Upload className="w-6 h-6 text-gray-400" />
                      </div>
                      <div>
                        <p className="text-gray-600">
                          <span className="font-medium text-primary-600">Click to upload</span> or drag and drop
                        </p>
                        <p className="text-xs text-gray-500">PNG, JPG up to 2MB each (max 5 images)</p>
                      </div>
                    </div>
                  </div>

                  {/* Image Preview */}
                  {imagePreview.length > 0 && (
                    <div className="grid grid-cols-5 gap-3">
                      {imagePreview.map((preview, index) => (
                        <motion.div
                          key={preview.id}
                          initial={{ opacity: 0, scale: 0.8 }}
                          animate={{ opacity: 1, scale: 1 }}
                          className="relative group"
                        >
                          <img
                            src={preview.url}
                            alt={`Preview ${index + 1}`}
                            className="w-full h-20 object-cover rounded-lg"
                          />
                          <button
                            type="button"
                            onClick={() => removeImage(index)}
                            className="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                          >
                            <X className="w-3 h-3" />
                          </button>
                        </motion.div>
                      ))}
                    </div>
                  )}
                  
                  {errors.images && <p className="text-red-600 text-sm">{errors.images}</p>}
                </div>

                {/* Address (Optional) */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Address (Optional)
                  </label>
                  <input
                    type="text"
                    name="address"
                    value={formData.address}
                    onChange={handleInputChange}
                    placeholder="Street address or landmark description"
                    className="input-field"
                  />
                  <p className="text-xs text-gray-500 mt-1">
                    Help others locate this issue more easily
                  </p>
                </div>
              </form>
            </div>

            {/* Footer */}
            <div className="border-t border-gray-200 px-6 py-4 bg-gray-50">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-2 text-sm text-gray-600">
                  <Info className="w-4 h-4" />
                  <span>Your report will be reviewed by local authorities</span>
                </div>
                <div className="flex space-x-3">
                  <motion.button
                    type="button"
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    onClick={onClose}
                    className="btn-secondary"
                    disabled={creating || updating}
                  >
                    Cancel
                  </motion.button>
                  <motion.button
                    type="button"
                    onClick={handleSubmit}
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    disabled={creating || updating || (!location && !isEditing)}
                    className="btn-primary flex items-center space-x-2 min-w-[140px] justify-center"
                  >
                    {(creating || updating) ? (
                      <>
                        <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                        <span>{isEditing ? 'Updating...' : 'Submitting...'}</span>
                      </>
                    ) : (
                      <>
                        <Send className="w-4 h-4" />
                        <span>{isEditing ? 'Update Report' : 'Submit Report'}</span>
                      </>
                    )}
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

export default ReportFormModal