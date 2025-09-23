import React, { useState, useCallback, useRef, useEffect } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { MapContainer, TileLayer, Marker, Popup, useMapEvents } from 'react-leaflet'
import L from 'leaflet'
import { Plus, MapPin, TreePine, AlertTriangle, CheckCircle, X, Send, Search } from 'lucide-react'
import { useReports } from '../hooks/useReports'
import ReportFormModal from '../components/reports/ReportFormModal'
import MapSearchBar from '../components/map/MapSearchBar'
import 'leaflet/dist/leaflet.css'

// Fix Leaflet default icon URLs for Vite
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
import markerIcon from 'leaflet/dist/images/marker-icon.png'
import markerShadow from 'leaflet/dist/images/marker-shadow.png'

L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
})

// Map click handler component
const MapEventHandler = ({ onMapClick }) => {
  useMapEvents({
    click: onMapClick,
  })
  return null
}

const MapPage = () => {
  const [viewState, setViewState] = useState({
    longitude: 2.3522, // Paris coordinates
    latitude: 48.8566,
    zoom: 12
  })
  
  const [showAddReport, setShowAddReport] = useState(false)
  const [newReportLocation, setNewReportLocation] = useState(null)
  const [showSearch, setShowSearch] = useState(false)
  const [editingReport, setEditingReport] = useState(null)
  const [showEditModal, setShowEditModal] = useState(false)
  const mapRef = useRef(null)
  
  // Fetch reports from API
  const { reports, loading, error, refresh, deleteReport, updateReport } = useReports()

  const handleMapClick = useCallback((event) => {
    if (showAddReport) {
      setNewReportLocation({
        longitude: event.latlng.lng,
        latitude: event.latlng.lat
      })
    }
  }, [showAddReport])

  const handleReportSuccess = (newReport) => {
    // Handle successful report submission
    console.log('Report submitted successfully:', newReport)
    setNewReportLocation(null)
    setShowAddReport(false)
    
    // Refresh the reports to show the new one on the map immediately
    setTimeout(() => {
      refresh()
    }, 100) // Small delay to ensure database is updated
  }

  const handleLocationSelect = (location) => {
    // Update the map view to the selected location
    setViewState({
      longitude: location.longitude,
      latitude: location.latitude,
      zoom: location.zoom || 15
    })

    // Get the map instance and fly to the new location
    if (mapRef.current) {
      const map = mapRef.current
      map.flyTo([location.latitude, location.longitude], location.zoom || 15, {
        duration: 1.5 // Animation duration in seconds
      })
    }

    // Close the search bar
    setShowSearch(false)
  }

  const handleEditReport = (report) => {
    setEditingReport(report)
    setShowEditModal(true)
    // Close any open popups
    if (mapRef.current) {
      mapRef.current.closePopup()
    }
  }

  const handleDeleteReport = async (report) => {
    if (window.confirm(`Are you sure you want to delete "${report.title}"?`)) {
      try {
        const result = await deleteReport(report.id)
        console.log('Delete result:', result) // Debug log
        
        if (result.success) {
          // Close any open popups
          if (mapRef.current) {
            mapRef.current.closePopup()
          }
          console.log('Report deleted successfully, refreshing list...')
          // Refresh reports to update the map immediately
          refresh()
        } else {
          console.error('Delete failed:', result)
          alert('Error deleting report: ' + (result.message || 'Unknown error'))
        }
      } catch (error) {
        console.error('Error deleting report:', error)
        alert('Error deleting report. Please try again.')
      }
    }
  }

  const handleUpdateSuccess = (updatedReport) => {
    console.log('Report updated successfully:', updatedReport)
    setEditingReport(null)
    setShowEditModal(false)
    // Refresh reports to show the updated data
    refresh()
  }

  const getMarkerColor = (report) => {
    // Color based on urgency
    switch (report.urgency) {
      case 'high': return '#ef4444' // red
      case 'medium': return '#f59e0b' // orange
      case 'low': return '#22c55e' // green
      default: return '#6b7280' // gray
    }
  }

  const getMarkerIcon = (report) => {
    // Icon based on type
    switch (report.type) {
      case 'tree_planting': return TreePine
      case 'maintenance': return AlertTriangle
      case 'pollution': return AlertTriangle
      case 'green_space_suggestion': return CheckCircle
      default: return MapPin
    }
  }

  const getIconSVG = (report) => {
    switch (report.type) {
      case 'tree_planting':
        return '<path d="M12 2L8 8h8l-4-6zM10 8l2-3 2 3H10zM9 10l3-4.5L15 10H9zM8 12l4-6 4 6H8zM6 14l6-9 6 9H6zM12 22v-8"/>'
      case 'maintenance':
      case 'pollution':
        return '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'
      case 'green_space_suggestion':
        return '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
      default:
        return '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>'
    }
  }

  return (
    <div className="relative h-full w-full">
      {/* Map */}
      <MapContainer
        center={[viewState.latitude, viewState.longitude]}
        zoom={viewState.zoom}
        style={{ width: '100%', height: '100%' }}
        className="z-0"
        ref={mapRef}
      >
        <TileLayer
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        />
        
        <MapEventHandler onMapClick={handleMapClick} />

        {/* Report Markers */}
        {reports && reports.map((report) => {
          return (
            <Marker
              key={report.id}
              position={[parseFloat(report.latitude), parseFloat(report.longitude)]}
              icon={L.divIcon({
                html: `<div class="w-10 h-10 rounded-full shadow-lg flex items-center justify-center cursor-pointer" style="background-color: ${getMarkerColor(report)}">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    ${getIconSVG(report)}
                  </svg>
                </div>`,
                className: 'custom-div-icon',
                iconSize: [40, 40],
                iconAnchor: [20, 40]
              })}
            >
              <Popup closeButton={false} className="custom-popup">
                <div className="p-4 max-w-sm">
                  <div className="flex items-start justify-between mb-3">
                    <h3 className="font-semibold text-gray-900">{report.title}</h3>
                    <button
                      onClick={(e) => {
                        e.preventDefault()
                        e.target.closest('.leaflet-popup').style.display = 'none'
                      }}
                      className="text-gray-400 hover:text-gray-600"
                    >
                      ✕
                    </button>
                  </div>
                  
                  <p className="text-sm text-gray-600 mb-3">{report.description}</p>
                  
                  {/* Images Preview */}
                  {report.image_urls && report.image_urls.length > 0 && (
                    <div className="mb-3">
                      <div className="flex space-x-2 overflow-x-auto">
                        {report.image_urls.slice(0, 2).map((imageUrl, index) => (
                          <img
                            key={index}
                            src={imageUrl}
                            alt={`Report image ${index + 1}`}
                            className="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                          />
                        ))}
                        {report.image_urls.length > 2 && (
                          <div className="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <span className="text-xs text-gray-500">+{report.image_urls.length - 2}</span>
                          </div>
                        )}
                      </div>
                    </div>
                  )}
                  
                  <div className="space-y-2">
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Type:</span>
                      <span className="font-medium capitalize">{report.type.replace('_', ' ')}</span>
                    </div>
                    
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Urgency:</span>
                      <span className={`font-medium capitalize px-2 py-1 rounded-full text-xs ${
                        report.urgency === 'high' ? 'bg-red-100 text-red-800' : 
                        report.urgency === 'medium' ? 'bg-orange-100 text-orange-800' :
                        'bg-green-100 text-green-800'
                      }`}>
                        {report.urgency}
                      </span>
                    </div>
                    
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Status:</span>
                      <span className={`font-medium capitalize px-2 py-1 rounded-full text-xs ${
                        report.status === 'completed' ? 'bg-green-100 text-green-800' :
                        report.status === 'validated' ? 'bg-blue-100 text-blue-800' :
                        report.status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-gray-100 text-gray-800'
                      }`}>
                        {report.status}
                      </span>
                    </div>
                    
                    {report.user && (
                      <div className="flex justify-between text-sm">
                        <span className="text-gray-500">Reported by:</span>
                        <span className="font-medium">{report.user.name}</span>
                      </div>
                    )}
                    
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Date:</span>
                      <span>{new Date(report.created_at).toLocaleDateString()}</span>
                    </div>
                    
                    {report.address && (
                      <div className="text-sm">
                        <span className="text-gray-500">Location:</span>
                        <p className="font-medium mt-1">{report.address}</p>
                      </div>
                    )}
                    
                    {/* CRUD Actions */}
                    <div className="flex gap-2 mt-4 pt-3 border-t border-gray-200">
                      <button
                        onClick={(e) => {
                          e.preventDefault()
                          handleEditReport(report)
                        }}
                        className="flex-1 bg-blue-600 text-white py-2 px-3 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-1"
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                      </button>
                      <button
                        onClick={(e) => {
                          e.preventDefault()
                          handleDeleteReport(report)
                        }}
                        className="flex-1 bg-red-600 text-white py-2 px-3 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center gap-1"
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
              </Popup>
            </Marker>
          )
        })}

        {/* New Report Location Marker */}
        {newReportLocation && (
          <Marker
            position={[newReportLocation.latitude, newReportLocation.longitude]}
            icon={L.divIcon({
              html: `<div class="w-10 h-10 bg-blue-500 rounded-full shadow-lg flex items-center justify-center animate-pulse">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
              </div>`,
              className: 'custom-div-icon',
              iconSize: [40, 40],
              iconAnchor: [20, 40]
            })}
          />
        )}
      </MapContainer>

      {/* Floating Action Button */}
      <motion.button
        onClick={() => {
          setShowAddReport(!showAddReport)
          setNewReportLocation(null)
        }}
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.9 }}
        className={`fixed bottom-6 right-6 w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-white transition-all duration-300 ${
          showAddReport ? 'bg-red-500 hover:bg-red-600 rotate-45' : 'bg-primary-600 hover:bg-primary-700'
        }`}
      >
        <Plus className="w-6 h-6" />
      </motion.button>

      {/* Search Button */}
      <motion.button
        onClick={() => setShowSearch(!showSearch)}
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.9 }}
        className={`fixed bottom-24 right-6 w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-white transition-all duration-300 ${
          showSearch ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-600 hover:bg-gray-700'
        }`}
      >
        <Search className="w-6 h-6" />
      </motion.button>

      {/* Search Bar */}
      <AnimatePresence>
        {showSearch && (
          <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 20, scale: 0.95 }}
            className="fixed top-6 right-6 z-50"
          >
            <MapSearchBar
              onLocationSelect={handleLocationSelect}
              onClose={() => setShowSearch(false)}
            />
          </motion.div>
        )}
      </AnimatePresence>

      {/* Add Report Instructions */}
      <AnimatePresence>
        {showAddReport && !newReportLocation && (
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 20 }}
            className="fixed bottom-24 right-6 bg-white rounded-lg shadow-xl p-4 max-w-xs"
          >
            <div className="flex items-center space-x-2 mb-2">
              <MapPin className="w-5 h-5 text-primary-600" />
              <span className="font-medium text-gray-900">Add Report</span>
            </div>
            <p className="text-sm text-gray-600">
              Click anywhere on the map to report an issue or suggest a location for greening.
            </p>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Modern Report Form Modal */}
      <ReportFormModal
        isOpen={showAddReport && newReportLocation}
        onClose={() => {
          setNewReportLocation(null)
          setShowAddReport(false)
        }}
        location={newReportLocation}
        onSuccess={handleReportSuccess}
      />

      {/* Edit Report Modal */}
      <ReportFormModal
        isOpen={showEditModal}
        onClose={() => {
          setEditingReport(null)
          setShowEditModal(false)
        }}
        existingReport={editingReport}
        onSuccess={handleUpdateSuccess}
        isEditing={true}
      />

      {/* Map Legend */}
      <motion.div
        initial={{ opacity: 0, x: -20 }}
        animate={{ opacity: 1, x: 0 }}
        className="fixed top-6 left-6 bg-white rounded-lg shadow-lg p-4"
      >
        <h3 className="font-semibold text-gray-900 mb-3">Map Legend</h3>
        <div className="space-y-2">
          <div className="flex items-center space-x-2">
            <div className="w-4 h-4 bg-green-500 rounded-full"></div>
            <span className="text-sm text-gray-600">Active Projects</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-4 h-4 bg-orange-500 rounded-full"></div>
            <span className="text-sm text-gray-600">Reports</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-4 h-4 bg-emerald-500 rounded-full"></div>
            <span className="text-sm text-gray-600">Green Spaces</span>
          </div>
        </div>
      </motion.div>
    </div>
  )
}

export default MapPage