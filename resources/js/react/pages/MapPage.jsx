import React, { useState, useCallback } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { MapContainer, TileLayer, Marker, Popup, useMapEvents } from 'react-leaflet'
import L from 'leaflet'
import { Plus, MapPin, TreePine, AlertTriangle, CheckCircle, X, Send } from 'lucide-react'
import { mapMarkers } from '../data/mockData'
import ReportFormModal from '../components/reports/ReportFormModal'
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
    longitude: -73.985130,
    latitude: 40.748817,
    zoom: 12
  })
  
  const [showAddReport, setShowAddReport] = useState(false)
  const [newReportLocation, setNewReportLocation] = useState(null)

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
    
    // You can add the new report to your local state here if needed
    // or show a success notification
  }

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

  const getIconSVG = (marker) => {
    if (marker.type === 'project') {
      return '<path d="M12 2L13.09 8.26L22 9L17 14L18.18 22L12 19.24L5.82 22L7 14L2 9L10.91 8.26L12 2Z"/>'
    } else if (marker.type === 'report') {
      return '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'
    } else {
      return '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
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
      >
        <TileLayer
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        />
        
        <MapEventHandler onMapClick={handleMapClick} />

        {/* Markers */}
        {mapMarkers.map((marker) => {
          return (
            <Marker
              key={marker.id}
              position={[marker.coordinates[1], marker.coordinates[0]]}
              icon={L.divIcon({
                html: `<div class="w-10 h-10 rounded-full shadow-lg flex items-center justify-center cursor-pointer" style="background-color: ${getMarkerColor(marker)}">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    ${getIconSVG(marker)}
                  </svg>
                </div>`,
                className: 'custom-div-icon',
                iconSize: [40, 40],
                iconAnchor: [20, 40]
              })}
            >
              <Popup closeButton={false} className="custom-popup">
                <div className="p-4">
                  <div className="flex items-start justify-between mb-3">
                    <h3 className="font-semibold text-gray-900">{marker.title}</h3>
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
                  
                  <p className="text-sm text-gray-600 mb-3">{marker.description}</p>
                  
                  <div className="space-y-2">
                    {marker.type === 'project' && (
                      <>
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-500">Progress:</span>
                          <span className="font-medium">{marker.progress}%</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                          <div
                            className="bg-green-500 h-2 rounded-full"
                            style={{ width: `${marker.progress}%` }}
                          />
                        </div>
                        <button className="w-full bg-green-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors mt-3">
                          View Project Details
                        </button>
                      </>
                    )}
                    
                    {marker.type === 'report' && (
                      <>
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-500">Urgency:</span>
                          <span className={`font-medium capitalize ${
                            marker.urgency === 'high' ? 'text-red-600' : 'text-orange-600'
                          }`}>
                            {marker.urgency}
                          </span>
                        </div>
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-500">Reported by:</span>
                          <span className="font-medium">{marker.reporter}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-500">Date:</span>
                          <span>{marker.reportDate}</span>
                        </div>
                        <button className="w-full bg-orange-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors mt-3">
                          Validate Report
                        </button>
                      </>
                    )}
                    
                    {marker.type === 'green-space' && (
                      <>
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-500">Area:</span>
                          <span className="font-medium">{marker.area}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-500">Trees:</span>
                          <span className="font-medium">{marker.treeCount}</span>
                        </div>
                      </>
                    )}
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