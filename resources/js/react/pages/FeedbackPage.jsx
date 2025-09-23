import React, { useState } from 'react'
import { motion } from 'framer-motion'
import { Star, Send, MessageCircle, ThumbsUp, Filter, Search } from 'lucide-react'
import { feedbackData, projects, events } from '../data/mockData'

const FeedbackPage = () => {
  const [activeTab, setActiveTab] = useState('give') // 'give' or 'view'
  const [feedbackType, setFeedbackType] = useState('project')
  const [selectedItem, setSelectedItem] = useState('')
  const [rating, setRating] = useState(0)
  const [hoverRating, setHoverRating] = useState(0)
  const [comment, setComment] = useState('')
  const [filterType, setFilterType] = useState('all')
  const [searchTerm, setSearchTerm] = useState('')

  const handleSubmitFeedback = (e) => {
    e.preventDefault()
    if (!selectedItem || !rating || !comment.trim()) {
      alert('Please fill in all fields')
      return
    }

    // Here you would submit to your backend
    console.log('Submitting feedback:', {
      type: feedbackType,
      item: selectedItem,
      rating,
      comment
    })

    // Reset form
    setSelectedItem('')
    setRating(0)
    setComment('')
    alert('Feedback submitted successfully!')
  }

  const filteredFeedback = feedbackData.filter(feedback => {
    const matchesSearch = feedback.comment.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         feedback.projectTitle?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         feedback.eventTitle?.toLowerCase().includes(searchTerm.toLowerCase())
    const matchesFilter = filterType === 'all' || feedback.type === filterType
    return matchesSearch && matchesFilter
  })

  const getAverageRating = () => {
    if (feedbackData.length === 0) return 0
    const total = feedbackData.reduce((sum, feedback) => sum + feedback.rating, 0)
    return (total / feedbackData.length).toFixed(1)
  }

  const getRatingDistribution = () => {
    const distribution = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 }
    feedbackData.forEach(feedback => {
      distribution[feedback.rating]++
    })
    return distribution
  }

  const ratingDistribution = getRatingDistribution()

  return (
    <div className="p-6 space-y-8">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
      >
        <h1 className="text-3xl font-bold text-gray-900 mb-2">Feedback & Reviews</h1>
        <p className="text-gray-600">
          Share your experience and help improve our community projects
        </p>
      </motion.div>

      {/* Tabs */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="bg-white rounded-xl border border-gray-200 p-6"
      >
        <div className="flex space-x-1 bg-gray-100 rounded-lg p-1 w-fit">
          <button
            onClick={() => setActiveTab('give')}
            className={`px-6 py-2 rounded-lg text-sm font-medium transition-colors ${
              activeTab === 'give' 
                ? 'bg-white text-gray-900 shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'
            }`}
          >
            Give Feedback
          </button>
          <button
            onClick={() => setActiveTab('view')}
            className={`px-6 py-2 rounded-lg text-sm font-medium transition-colors ${
              activeTab === 'view' 
                ? 'bg-white text-gray-900 shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'
            }`}
          >
            View Feedback
          </button>
        </div>
      </motion.div>

      {/* Give Feedback Tab */}
      {activeTab === 'give' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="bg-white rounded-xl border border-gray-200 p-8"
        >
          <h2 className="text-2xl font-bold text-gray-900 mb-6">Share Your Experience</h2>
          
          <form onSubmit={handleSubmitFeedback} className="space-y-6">
            {/* Feedback Type */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-3">
                What would you like to review?
              </label>
              <div className="flex space-x-4">
                <label className="flex items-center">
                  <input
                    type="radio"
                    value="project"
                    checked={feedbackType === 'project'}
                    onChange={(e) => {
                      setFeedbackType(e.target.value)
                      setSelectedItem('')
                    }}
                    className="mr-2"
                  />
                  <span>Project</span>
                </label>
                <label className="flex items-center">
                  <input
                    type="radio"
                    value="event"
                    checked={feedbackType === 'event'}
                    onChange={(e) => {
                      setFeedbackType(e.target.value)
                      setSelectedItem('')
                    }}
                    className="mr-2"
                  />
                  <span>Event</span>
                </label>
              </div>
            </div>

            {/* Select Project/Event */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Select {feedbackType === 'project' ? 'Project' : 'Event'}
              </label>
              <select
                value={selectedItem}
                onChange={(e) => setSelectedItem(e.target.value)}
                className="input-field"
                required
              >
                <option value="">Choose a {feedbackType}...</option>
                {feedbackType === 'project' 
                  ? projects.map(project => (
                      <option key={project.id} value={project.id}>
                        {project.title}
                      </option>
                    ))
                  : events.map(event => (
                      <option key={event.id} value={event.id}>
                        {event.title}
                      </option>
                    ))
                }
              </select>
            </div>

            {/* Rating */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-3">
                Your Rating
              </label>
              <div className="flex space-x-2">
                {[1, 2, 3, 4, 5].map((star) => (
                  <button
                    key={star}
                    type="button"
                    onMouseEnter={() => setHoverRating(star)}
                    onMouseLeave={() => setHoverRating(0)}
                    onClick={() => setRating(star)}
                    className="text-3xl transition-colors"
                  >
                    <Star
                      className={`w-8 h-8 ${
                        star <= (hoverRating || rating)
                          ? 'text-yellow-400 fill-current'
                          : 'text-gray-300'
                      }`}
                    />
                  </button>
                ))}
              </div>
              {rating > 0 && (
                <p className="text-sm text-gray-600 mt-2">
                  {rating === 5 ? 'Excellent!' : 
                   rating === 4 ? 'Very Good' :
                   rating === 3 ? 'Good' :
                   rating === 2 ? 'Fair' : 'Poor'}
                </p>
              )}
            </div>

            {/* Comment */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Your Review
              </label>
              <textarea
                value={comment}
                onChange={(e) => setComment(e.target.value)}
                placeholder="Share your experience, what you liked, and any suggestions for improvement..."
                rows={5}
                className="input-field"
                required
              />
              <p className="text-sm text-gray-500 mt-1">
                {comment.length}/500 characters
              </p>
            </div>

            <motion.button
              type="submit"
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
              className="btn-primary flex items-center"
            >
              <Send className="w-4 h-4 mr-2" />
              Submit Feedback
            </motion.button>
          </form>
        </motion.div>
      )}

      {/* View Feedback Tab */}
      {activeTab === 'view' && (
        <div className="space-y-8">
          {/* Feedback Summary */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
            className="bg-white rounded-xl border border-gray-200 p-8"
          >
            <h2 className="text-2xl font-bold text-gray-900 mb-6">Community Feedback Summary</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {/* Average Rating */}
              <div className="text-center">
                <div className="text-4xl font-bold text-gray-900 mb-2">
                  {getAverageRating()}
                </div>
                <div className="flex justify-center space-x-1 mb-2">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <Star
                      key={star}
                      className={`w-5 h-5 ${
                        star <= Math.round(getAverageRating())
                          ? 'text-yellow-400 fill-current'
                          : 'text-gray-300'
                      }`}
                    />
                  ))}
                </div>
                <p className="text-sm text-gray-600">
                  Based on {feedbackData.length} reviews
                </p>
              </div>

              {/* Rating Distribution */}
              <div className="md:col-span-2">
                <h3 className="font-semibold text-gray-900 mb-4">Rating Distribution</h3>
                <div className="space-y-2">
                  {[5, 4, 3, 2, 1].map((rating) => (
                    <div key={rating} className="flex items-center space-x-3">
                      <span className="text-sm text-gray-600 w-8">
                        {rating} ⭐
                      </span>
                      <div className="flex-1 bg-gray-200 rounded-full h-2">
                        <div
                          className="bg-yellow-400 h-2 rounded-full transition-all duration-500"
                          style={{ 
                            width: `${feedbackData.length ? (ratingDistribution[rating] / feedbackData.length) * 100 : 0}%` 
                          }}
                        />
                      </div>
                      <span className="text-sm text-gray-600 w-8">
                        {ratingDistribution[rating]}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </motion.div>

          {/* Filters */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3 }}
            className="bg-white rounded-xl border border-gray-200 p-6"
          >
            <div className="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
              <div className="flex space-x-4">
                <select
                  value={filterType}
                  onChange={(e) => setFilterType(e.target.value)}
                  className="input-field w-48"
                >
                  <option value="all">All Reviews</option>
                  <option value="project">Project Reviews</option>
                  <option value="event">Event Reviews</option>
                </select>
              </div>

              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search className="h-5 w-5 text-gray-400" />
                </div>
                <input
                  type="text"
                  placeholder="Search reviews..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="input-field pl-10 w-64"
                />
              </div>
            </div>
          </motion.div>

          {/* Feedback List */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ delay: 0.4 }}
            className="space-y-6"
          >
            {filteredFeedback.map((feedback, index) => (
              <motion.div
                key={feedback.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.1 * index }}
                className="bg-white rounded-xl border border-gray-200 p-6"
              >
                <div className="flex items-start space-x-4">
                  <img
                    src={feedback.avatar}
                    alt={feedback.user}
                    className="w-12 h-12 rounded-full"
                  />
                  
                  <div className="flex-1">
                    <div className="flex items-center justify-between mb-2">
                      <h3 className="font-semibold text-gray-900">{feedback.user}</h3>
                      <span className="text-sm text-gray-500">{feedback.date}</span>
                    </div>
                    
                    <div className="flex items-center space-x-2 mb-2">
                      <div className="flex space-x-1">
                        {[1, 2, 3, 4, 5].map((star) => (
                          <Star
                            key={star}
                            className={`w-4 h-4 ${
                              star <= feedback.rating
                                ? 'text-yellow-400 fill-current'
                                : 'text-gray-300'
                            }`}
                          />
                        ))}
                      </div>
                      <span className="text-sm text-gray-600">
                        {feedback.rating}/5
                      </span>
                    </div>
                    
                    <div className="mb-3">
                      <span className="text-sm text-primary-600 bg-primary-50 px-2 py-1 rounded-full">
                        {feedback.type === 'project' ? feedback.projectTitle : feedback.eventTitle}
                      </span>
                    </div>
                    
                    <p className="text-gray-600 mb-4">{feedback.comment}</p>
                    
                    <div className="flex items-center space-x-4 text-sm text-gray-500">
                      <button className="flex items-center space-x-1 hover:text-primary-600 transition-colors">
                        <ThumbsUp className="w-4 h-4" />
                        <span>Helpful (5)</span>
                      </button>
                      <button className="flex items-center space-x-1 hover:text-primary-600 transition-colors">
                        <MessageCircle className="w-4 h-4" />
                        <span>Reply</span>
                      </button>
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}
          </motion.div>

          {/* Empty State */}
          {filteredFeedback.length === 0 && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-12"
            >
              <div className="w-24 h-24 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <MessageCircle className="w-8 h-8 text-gray-400" />
              </div>
              <h3 className="text-lg font-medium text-gray-900 mb-2">No feedback found</h3>
              <p className="text-gray-600">
                Try adjusting your search or filters to find more reviews.
              </p>
            </motion.div>
          )}
        </div>
      )}
    </div>
  )
}

export default FeedbackPage