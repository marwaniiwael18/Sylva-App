import React, { useState, useRef, useEffect } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { Search, MapPin, Loader2, X } from 'lucide-react'

const MapSearchBar = ({ onLocationSelect, onClose }) => {
  const [searchQuery, setSearchQuery] = useState('')
  const [searchResults, setSearchResults] = useState([])
  const [isLoading, setIsLoading] = useState(false)
  const [isOpen, setIsOpen] = useState(false)
  const searchTimeoutRef = useRef(null)
  const inputRef = useRef(null)

  // Focus input when component mounts
  useEffect(() => {
    if (inputRef.current) {
      inputRef.current.focus()
    }
  }, [])

  // Debounced search function
  useEffect(() => {
    if (searchQuery.trim().length < 3) {
      setSearchResults([])
      setIsOpen(false)
      return
    }

    // Clear previous timeout
    if (searchTimeoutRef.current) {
      clearTimeout(searchTimeoutRef.current)
    }

    // Set new timeout for search
    searchTimeoutRef.current = setTimeout(() => {
      performSearch(searchQuery)
    }, 300)

    return () => {
      if (searchTimeoutRef.current) {
        clearTimeout(searchTimeoutRef.current)
      }
    }
  }, [searchQuery])

  const performSearch = async (query) => {
    if (!query.trim()) return

    setIsLoading(true)
    
    try {
      // Using Nominatim (OpenStreetMap) geocoding service
      const response = await fetch(
        `https://nominatim.openstreetmap.org/search?` +
        `q=${encodeURIComponent(query)}&` +
        `format=json&` +
        `limit=5&` +
        `addressdetails=1&` +
        `countrycodes=us,ca,gb,fr,de,it,es&` +
        `accept-language=en`
      )

      if (!response.ok) throw new Error('Search failed')

      const data = await response.json()
      
      const formattedResults = data.map((place) => ({
        id: place.place_id,
        name: place.display_name.split(',')[0], // First part is usually the main name
        address: place.display_name,
        lat: parseFloat(place.lat),
        lon: parseFloat(place.lon),
        type: place.type,
        category: place.class,
        importance: place.importance || 0
      }))

      setSearchResults(formattedResults)
      setIsOpen(true)
    } catch (error) {
      console.error('Search error:', error)
      setSearchResults([])
    } finally {
      setIsLoading(false)
    }
  }

  const handleResultSelect = (result) => {
    setSearchQuery(result.name)
    setIsOpen(false)
    setSearchResults([])
    
    if (onLocationSelect) {
      onLocationSelect({
        latitude: result.lat,
        longitude: result.lon,
        name: result.name,
        address: result.address,
        zoom: 15 // Zoom level when selecting a location
      })
    }
  }

  const handleClear = () => {
    setSearchQuery('')
    setSearchResults([])
    setIsOpen(false)
    if (inputRef.current) {
      inputRef.current.focus()
    }
  }

  const getPlaceIcon = (category, type) => {
    // Different icons based on place type
    if (category === 'amenity') {
      return '🏢'
    } else if (category === 'natural') {
      return '🌲'
    } else if (category === 'place') {
      return '🏙️'
    } else if (category === 'highway' || category === 'street') {
      return '🛣️'
    } else if (category === 'building') {
      return '🏗️'
    } else {
      return '📍'
    }
  }

  return (
    <div className="relative w-full max-w-md">
      {/* Search Input */}
      <div className="relative">
        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <Search className="h-5 w-5 text-gray-400" />
        </div>
        
        <input
          ref={inputRef}
          type="text"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          placeholder="Search for places..."
          className="w-full pl-10 pr-16 py-3 bg-white border border-gray-200 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all duration-200"
        />

        {/* Loading/Clear Button */}
        <div className="absolute inset-y-0 right-0 flex items-center">
          {isLoading ? (
            <div className="pr-3">
              <Loader2 className="h-5 w-5 text-gray-400 animate-spin" />
            </div>
          ) : searchQuery ? (
            <button
              onClick={handleClear}
              className="pr-3 text-gray-400 hover:text-gray-600 transition-colors"
            >
              <X className="h-5 w-5" />
            </button>
          ) : null}
          
          {/* Close Search Bar Button */}
          <button
            onClick={onClose}
            className="px-3 py-1 text-gray-400 hover:text-gray-600 border-l border-gray-200"
          >
            <X className="h-5 w-5" />
          </button>
        </div>
      </div>

      {/* Search Results Dropdown */}
      <AnimatePresence>
        {isOpen && searchResults.length > 0 && (
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            className="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
          >
            <div className="max-h-64 overflow-y-auto">
              {searchResults.map((result, index) => (
                <motion.button
                  key={result.id}
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: index * 0.05 }}
                  onClick={() => handleResultSelect(result)}
                  className="w-full px-4 py-3 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none transition-colors border-b border-gray-100 last:border-b-0"
                >
                  <div className="flex items-center space-x-3">
                    <span className="text-lg flex-shrink-0">
                      {getPlaceIcon(result.category, result.type)}
                    </span>
                    <div className="flex-1 min-w-0">
                      <div className="font-medium text-gray-900 truncate">
                        {result.name}
                      </div>
                      <div className="text-sm text-gray-500 truncate">
                        {result.address}
                      </div>
                    </div>
                    <MapPin className="h-4 w-4 text-gray-400 flex-shrink-0" />
                  </div>
                </motion.button>
              ))}
            </div>
            
            {/* Attribution */}
            <div className="px-4 py-2 bg-gray-50 border-t border-gray-100">
              <div className="text-xs text-gray-500">
                Powered by OpenStreetMap
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* No Results Message */}
      <AnimatePresence>
        {isOpen && searchResults.length === 0 && searchQuery.trim().length >= 3 && !isLoading && (
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            className="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg p-4"
          >
            <div className="text-center text-gray-500">
              <MapPin className="h-8 w-8 mx-auto mb-2 text-gray-300" />
              <div className="text-sm">
                No places found for "{searchQuery}"
              </div>
              <div className="text-xs mt-1">
                Try a different search term
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  )
}

export default MapSearchBar