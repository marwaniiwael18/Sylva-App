import React, { useState, useRef, useCallback } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import Map, { Marker, Popup, NavigationControl, GeolocateControl } from 'react-map-gl'
import { Plus, MapPin, TreePine, AlertTriangle, CheckCircle, X, Send } from 'lucide-react'
import { mapMarkers } from '../data/mockData'
import 'mapbox-gl/dist/mapbox-gl.css'

// Mock Mapbox token - replace with your actual token
const MAPBOX_TOKEN = 'pk.eyJ1IjoieW91ci11c2VybmFtZSIsImEiOiJjbXl0b2tlbiJ9.your-token-here'

const MapPage = () => {
  const [viewState, setViewState] = useState({
    longitude: -73.985130,
    latitude: 40.748817,
    zoom: 12
  })
  
  const [selectedMarker, setSelectedMarker] = useState(null)
  const [showAddReport, setShowAddReport] = useState(false)
  const [newReportLocation, setNewReportLocation] = useState(null)
  const [reportForm, setReportForm] = useState({
    title: '',
    description: '',
    urgency: 'low',
    type: 'suggestion'
  })
  
  const mapRef = useRef()

  const handleMapClick = useCallback((event) => {
    if (showAddReport) {
      setNewReportLocation({
        longitude: event.lngLat.lng,
        latitude: event.lngLat.lat
      })
    }
  }, [showAddReport])

  const getMarkerColor = (marker) => {
    if (marker.type === 'project') {
      return marker.status === 'active' ? '#22c55e' : '#6b7280'
    } else if (marker.type === 'report') {
      return marker.urgency === 'high' ? '#ef4444' : '#f59e0b'
    } else {
      return '#10b981'
    }
  }

  const getMarkerIcon = (marker) => {
    if (marker.type === 'project') {
      return TreePine
    } else if (marker.type === 'report') {
      return AlertTriangle
    } else {
      return CheckCircle
    }
  }

  const handleSubmitReport = (e) => {
    e.preventDefault()
    // Here you would submit the report to your backend
    console.log('Submitting report:', {
      ...reportForm,
      location: newReportLocation
    })
    
    // Reset form and close modal
    setReportForm({
      title: '',
      description: '',
      urgency: 'low',
      type: 'suggestion'
    })
    setNewReportLocation(null)
    setShowAddReport(false)
    
    // Show success message (you can implement a toast notification here)
    alert('Report submitted successfully!')
  }

  return (
    <div className="relative h-full w-full">
      {/* Map */}
      <Map
        ref={mapRef}
        {...viewState}
        onMove={evt => setViewState(evt.viewState)}
        onClick={handleMapClick}
        mapboxAccessToken={MAPBOX_TOKEN}
        style={{ width: '100%', height: '100%' }}
        mapStyle="mapbox://styles/mapbox/streets-v12"
        attributionControl={false}
      >
        {/* Navigation Controls */}
        <NavigationControl position="top-right" />
        <GeolocateControl position="top-right" />

        {/* Markers */}
        {mapMarkers.map((marker) => {
          const IconComponent = getMarkerIcon(marker)
          return (
            <Marker
              key={marker.id}
              longitude={marker.coordinates[0]}
              latitude={marker.coordinates[1]}
              anchor="bottom"
            >
              <motion.div
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.9 }}
                className="cursor-pointer"
                onClick={(e) => {
                  e.stopPropagation()
                  setSelectedMarker(marker)
                }}
              >
                <div
                  className="w-10 h-10 rounded-full shadow-lg flex items-center justify-center"
                  style={{ backgroundColor: getMarkerColor(marker) }}
                >
                  <IconComponent className="w-5 h-5 text-white" />
                </div>
              </motion.div>
            </Marker>
          )
        })}

        {/* New Report Location Marker */}
        {newReportLocation && (
          <Marker
            longitude={newReportLocation.longitude}
            latitude={newReportLocation.latitude}
            anchor="bottom"
          >
            <div className="w-10 h-10 bg-blue-500 rounded-full shadow-lg flex items-center justify-center animate-pulse">
              <MapPin className="w-5 h-5 text-white" />
            </div>
          </Marker>
        )}

        {/* Popup */}
        {selectedMarker && (
          <Popup
            longitude={selectedMarker.coordinates[0]}
            latitude={selectedMarker.coordinates[1]}
            anchor="top"
            onClose={() => setSelectedMarker(null)}
            closeButton={false}
            className="max-w-xs"
          >
            <div className="p-4">
              <div className="flex items-start justify-between mb-3">
                <h3 className="font-semibold text-gray-900">{selectedMarker.title}</h3>
                <button
                  onClick={() => setSelectedMarker(null)}
                  className="text-gray-400 hover:text-gray-600"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
              
              <p className="text-sm text-gray-600 mb-3">{selectedMarker.description}</p>
              
              <div className="space-y-2">
                {selectedMarker.type === 'project' && (
                  <>
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Progress:</span>
                      <span className="font-medium">{selectedMarker.progress}%</span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-2">
                      <div
                        className="bg-green-500 h-2 rounded-full"
                        style={{ width: `${selectedMarker.progress}%` }}
                      />
                    </div>
                    <motion.button
                      whileHover={{ scale: 1.02 }}
                      whileTap={{ scale: 0.98 }}
                      className="w-full bg-primary-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors mt-3"
                    >
                      View Project Details
                    </motion.button>
                  </>
                )}
                
                {selectedMarker.type === 'report' && (
                  <>
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Urgency:</span>
                      <span className={`font-medium capitalize ${
                        selectedMarker.urgency === 'high' ? 'text-red-600' : 'text-orange-600'
                      }`}>
                        {selectedMarker.urgency}
                      </span>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Reported by:</span>
                      <span className="font-medium">{selectedMarker.reporter}</span>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Date:</span>
                      <span>{selectedMarker.reportDate}</span>
                    </div>
                    <motion.button
                      whileHover={{ scale: 1.02 }}
                      whileTap={{ scale: 0.98 }}
                      className="w-full bg-orange-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors mt-3"
                    >
                      Validate Report
                    </motion.button>
                  </>
                )}
                
                {selectedMarker.type === 'green-space' && (
                  <>
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Area:</span>
                      <span className="font-medium">{selectedMarker.area}</span>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-500">Trees:</span>
                      <span className="font-medium">{selectedMarker.treeCount}</span>
                    </div>
                  </>
                )}
              </div>
            </div>
          </Popup>
        )}
      </Map>

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

      {/* Add Report Form Modal */}
      <AnimatePresence>
        {showAddReport && newReportLocation && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
          >
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white rounded-xl p-6 w-full max-w-md"
            >
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-semibold text-gray-900">Submit Report</h3>
                <button
                  onClick={() => {
                    setNewReportLocation(null)
                    setShowAddReport(false)
                  }}
                  className="text-gray-400 hover:text-gray-600"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>

              <form onSubmit={handleSubmitReport} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Report Type
                  </label>
                  <select
                    value={reportForm.type}
                    onChange={(e) => setReportForm({...reportForm, type: e.target.value})}
                    className="input-field"
                  >
                    <option value="suggestion">Greening Suggestion</option>
                    <option value="issue">Environmental Issue</option>
                    <option value="maintenance">Maintenance Needed</option>
                  </select>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Title
                  </label>
                  <input
                    type="text"
                    value={reportForm.title}
                    onChange={(e) => setReportForm({...reportForm, title: e.target.value})}
                    placeholder="Brief description of the report"
                    className="input-field"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Description
                  </label>
                  <textarea
                    value={reportForm.description}
                    onChange={(e) => setReportForm({...reportForm, description: e.target.value})}
                    placeholder="Provide more details about your report..."
                    rows={3}
                    className="input-field"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Urgency Level
                  </label>
                  <div className="flex space-x-3">
                    {['low', 'medium', 'high'].map((level) => (
                      <label key={level} className="flex items-center">
                        <input
                          type="radio"
                          value={level}
                          checked={reportForm.urgency === level}
                          onChange={(e) => setReportForm({...reportForm, urgency: e.target.value})}
                          className="mr-2"
                        />
                        <span className={`text-sm capitalize ${
                          level === 'high' ? 'text-red-600' : 
                          level === 'medium' ? 'text-orange-600' : 'text-green-600'
                        }`}>
                          {level}
                        </span>
                      </label>
                    ))}
                  </div>
                </div>

                <div className="flex space-x-3 pt-4">
                  <button
                    type="button"
                    onClick={() => {
                      setNewReportLocation(null)
                      setShowAddReport(false)
                    }}
                    className="flex-1 btn-secondary"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 btn-primary flex items-center justify-center"
                  >
                    <Send className="w-4 h-4 mr-2" />
                    Submit Report
                  </button>
                </div>
              </form>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

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