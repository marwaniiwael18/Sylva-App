import React, { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { motion } from 'framer-motion'
import { 
  ArrowLeft, MapPin, Users, Calendar, Clock, User, CheckCircle,
  Share2, Heart, Star, AlertCircle, Wrench
} from 'lucide-react'
import { events } from '../data/mockData'

const EventDetailPage = () => {
  const { id } = useParams()
  const [isRegistered, setIsRegistered] = useState(false)
  const [showRegistrationModal, setShowRegistrationModal] = useState(false)

  const event = events.find(e => e.id === parseInt(id))

  if (!event) {
    return (
      <div className="p-6 text-center">
        <h1 className="text-2xl font-bold text-gray-900 mb-4">Event not found</h1>
        <Link to="/events" className="btn-primary">
          Back to Events
        </Link>
      </div>
    )
  }

  const handleRegister = () => {
    setIsRegistered(true)
    setShowRegistrationModal(false)
  }

  const getDifficultyColor = (difficulty) => {
    switch(difficulty.toLowerCase()) {
      case 'beginner': return 'bg-green-100 text-green-800'
      case 'intermediate': return 'bg-yellow-100 text-yellow-800'
      case 'advanced': return 'bg-red-100 text-red-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }

  const getCategoryIcon = (category) => {
    switch(category.toLowerCase()) {
      case 'workshop': return <Wrench className="w-5 h-5" />
      case 'festival': return <span className="text-xl">🎉</span>
      case 'conservation': return <span className="text-xl">🌿</span>
      default: return <Calendar className="w-5 h-5" />
    }
  }

  const getAttendanceStatus = () => {
    const percentage = (event.attendees / event.maxAttendees) * 100
    if (percentage >= 90) return { color: 'text-red-600', status: 'Almost Full!' }
    if (percentage >= 70) return { color: 'text-orange-600', status: 'Filling Up' }
    return { color: 'text-green-600', status: 'Available Spots' }
  }

  const attendanceStatus = getAttendanceStatus()

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <div className="relative h-96 overflow-hidden">
        <img
          src={event.image}
          alt={event.title}
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-black bg-opacity-40" />
        
        {/* Back Button */}
        <motion.div
          initial={{ opacity: 0, x: -20 }}
          animate={{ opacity: 1, x: 0 }}
          className="absolute top-6 left-6"
        >
          <Link
            to="/events"
            className="inline-flex items-center px-4 py-2 bg-white/90 backdrop-blur-sm rounded-lg text-gray-900 hover:bg-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4 mr-2" />
            Back to Events
          </Link>
        </motion.div>

        {/* Hero Content */}
        <div className="absolute bottom-0 left-0 right-0 p-8 text-white">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
          >
            <div className="flex items-center space-x-3 mb-4">
              <span className="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-xs font-medium border border-white/20 flex items-center">
                {getCategoryIcon(event.category)}
                <span className="ml-2">{event.category}</span>
              </span>
              <span className={`px-3 py-1 rounded-full text-xs font-medium ${getDifficultyColor(event.difficulty)}`}>
                {event.difficulty}
              </span>
            </div>
            <h1 className="text-4xl font-bold mb-4">{event.title}</h1>
            <div className="flex items-center space-x-6 text-white/90">
              <div className="flex items-center">
                <Calendar className="w-5 h-5 mr-2" />
                {event.date}
              </div>
              <div className="flex items-center">
                <Clock className="w-5 h-5 mr-2" />
                {event.time}
              </div>
              <div className="flex items-center">
                <MapPin className="w-5 h-5 mr-2" />
                {event.location}
              </div>
            </div>
          </motion.div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto p-6 -mt-12 relative z-10">
        <div className="grid grid-cols-1 xl:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="xl:col-span-2 space-y-8">
            {/* Event Overview */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-8"
            >
              <h2 className="text-2xl font-bold text-gray-900 mb-6">About This Event</h2>
              <p className="text-gray-600 text-lg leading-relaxed mb-8">
                {event.description}
              </p>

              {/* Organizer */}
              <div className="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg mb-6">
                <div className="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center">
                  <User className="w-6 h-6 text-white" />
                </div>
                <div>
                  <h3 className="font-semibold text-gray-900">Organized by</h3>
                  <p className="text-gray-600">{event.organizer}</p>
                </div>
              </div>

              {/* What to Expect */}
              <div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">What to Expect</h3>
                <div className="bg-blue-50 rounded-lg p-6">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <h4 className="font-medium text-gray-900 mb-2">Difficulty Level</h4>
                      <span className={`px-3 py-1 rounded-full text-sm font-medium ${getDifficultyColor(event.difficulty)}`}>
                        {event.difficulty}
                      </span>
                    </div>
                    <div>
                      <h4 className="font-medium text-gray-900 mb-2">Equipment</h4>
                      <p className="text-sm text-gray-600">{event.equipment}</p>
                    </div>
                  </div>
                </div>
              </div>
            </motion.div>

            {/* Requirements & Preparation */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-8"
            >
              <h2 className="text-2xl font-bold text-gray-900 mb-6">Requirements & Preparation</h2>
              
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900">What to Bring:</h3>
                <ul className="space-y-3">
                  {event.requirements.map((requirement, index) => (
                    <li key={index} className="flex items-center space-x-3">
                      <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0" />
                      <span className="text-gray-600">{requirement}</span>
                    </li>
                  ))}
                </ul>
              </div>

              <div className="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div className="flex items-start space-x-3">
                  <AlertCircle className="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" />
                  <div>
                    <h4 className="font-medium text-yellow-800 mb-1">Important Note</h4>
                    <p className="text-sm text-yellow-700">
                      Please arrive 15 minutes early for check-in and safety briefing. 
                      Event may be cancelled due to severe weather conditions.
                    </p>
                  </div>
                </div>
              </div>
            </motion.div>

            {/* Reviews & Testimonials */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.5 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-8"
            >
              <h2 className="text-2xl font-bold text-gray-900 mb-6">What Participants Say</h2>
              <div className="space-y-6">
                {/* Mock testimonials */}
                <div className="border-l-4 border-primary-500 pl-6">
                  <div className="flex items-center mb-2">
                    {[1, 2, 3, 4, 5].map((star) => (
                      <Star key={star} className="w-4 h-4 text-yellow-400 fill-current" />
                    ))}
                  </div>
                  <p className="text-gray-600 mb-2">
                    "Amazing experience! Learned so much about urban gardening and met wonderful people."
                  </p>
                  <p className="text-sm text-gray-500">- Sarah M., Previous Participant</p>
                </div>
                
                <div className="border-l-4 border-primary-500 pl-6">
                  <div className="flex items-center mb-2">
                    {[1, 2, 3, 4, 5].map((star) => (
                      <Star key={star} className="w-4 h-4 text-yellow-400 fill-current" />
                    ))}
                  </div>
                  <p className="text-gray-600 mb-2">
                    "Well organized event with knowledgeable instructors. Highly recommend!"
                  </p>
                  <p className="text-sm text-gray-500">- Michael R., Previous Participant</p>
                </div>
              </div>
            </motion.div>
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Registration Card */}
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.3 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6"
            >
              <div className="text-center mb-6">
                <h3 className="text-xl font-bold text-gray-900 mb-2">
                  {isRegistered ? 'You\'re Registered!' : 'Join This Event'}
                </h3>
                <p className="text-gray-600">
                  {isRegistered 
                    ? 'We\'re excited to see you there!'
                    : 'Secure your spot for this amazing event'
                  }
                </p>
              </div>

              {/* Attendance Info */}
              <div className="mb-6">
                <div className="flex items-center justify-between mb-2">
                  <span className="text-sm text-gray-600">Attendance</span>
                  <span className={`text-sm font-medium ${attendanceStatus.color}`}>
                    {attendanceStatus.status}
                  </span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-3 mb-2">
                  <div
                    className="bg-primary-500 h-3 rounded-full transition-all duration-300"
                    style={{ width: `${(event.attendees / event.maxAttendees) * 100}%` }}
                  />
                </div>
                <p className="text-sm text-gray-600">
                  {event.attendees} of {event.maxAttendees} spots filled
                </p>
              </div>

              {!isRegistered ? (
                <motion.button
                  onClick={() => setShowRegistrationModal(true)}
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                  className="w-full btn-primary"
                  disabled={event.attendees >= event.maxAttendees}
                >
                  {event.attendees >= event.maxAttendees ? 'Event Full' : 'Register Now'}
                </motion.button>
              ) : (
                <div className="space-y-3">
                  <div className="w-full bg-green-100 text-green-700 py-3 px-6 rounded-lg font-medium text-center flex items-center justify-center">
                    <CheckCircle className="w-5 h-5 mr-2" />
                    Registered
                  </div>
                  <button className="w-full btn-secondary">
                    Add to Calendar
                  </button>
                </div>
              )}

              <div className="flex space-x-3 mt-4">
                <button className="flex-1 btn-ghost">
                  <Share2 className="w-4 h-4 mr-2" />
                  Share
                </button>
                <button className="flex-1 btn-ghost">
                  <Heart className="w-4 h-4 mr-2" />
                  Save
                </button>
              </div>
            </motion.div>

            {/* Event Details Card */}
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.4 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
              <h3 className="text-lg font-bold text-gray-900 mb-4">Event Details</h3>
              <div className="space-y-4">
                <div className="flex items-start space-x-3">
                  <Calendar className="w-5 h-5 text-gray-400 mt-0.5" />
                  <div>
                    <div className="font-medium text-gray-900">{event.date}</div>
                    <div className="text-sm text-gray-600">{event.time}</div>
                  </div>
                </div>
                
                <div className="flex items-start space-x-3">
                  <MapPin className="w-5 h-5 text-gray-400 mt-0.5" />
                  <div>
                    <div className="font-medium text-gray-900">{event.location}</div>
                    <button className="text-sm text-primary-600 hover:text-primary-700">
                      View on map
                    </button>
                  </div>
                </div>
                
                <div className="flex items-start space-x-3">
                  <User className="w-5 h-5 text-gray-400 mt-0.5" />
                  <div>
                    <div className="font-medium text-gray-900">{event.organizer}</div>
                    <div className="text-sm text-gray-600">Event Organizer</div>
                  </div>
                </div>
                
                <div className="flex items-start space-x-3">
                  <Users className="w-5 h-5 text-gray-400 mt-0.5" />
                  <div>
                    <div className="font-medium text-gray-900">
                      {event.maxAttendees} max participants
                    </div>
                    <div className="text-sm text-gray-600">
                      {event.attendees} currently registered
                    </div>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </div>

      {/* Registration Modal */}
      {showRegistrationModal && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        >
          <motion.div
            initial={{ scale: 0.9, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            className="bg-white rounded-xl p-8 w-full max-w-md"
          >
            <div className="text-center mb-6">
              <div className="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <CheckCircle className="w-8 h-8 text-primary-600" />
              </div>
              <h3 className="text-2xl font-bold text-gray-900 mb-2">
                Confirm Registration
              </h3>
              <p className="text-gray-600">
                You're about to register for "{event.title}"
              </p>
            </div>

            <div className="bg-gray-50 rounded-lg p-4 mb-6">
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-gray-600">Event:</span>
                  <span className="font-medium">{event.title}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Date:</span>
                  <span className="font-medium">{event.date}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Time:</span>
                  <span className="font-medium">{event.time}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Location:</span>
                  <span className="font-medium">{event.location}</span>
                </div>
              </div>
            </div>

            <div className="flex space-x-3">
              <button
                onClick={() => setShowRegistrationModal(false)}
                className="flex-1 btn-secondary"
              >
                Cancel
              </button>
              <button
                onClick={handleRegister}
                className="flex-1 btn-primary"
              >
                Confirm Registration
              </button>
            </div>
          </motion.div>
        </motion.div>
      )}
    </div>
  )
}

export default EventDetailPage