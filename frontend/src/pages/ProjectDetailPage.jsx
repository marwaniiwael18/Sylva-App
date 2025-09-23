import React, { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { motion } from 'framer-motion'
import { 
  ArrowLeft, MapPin, Users, TreePine, Calendar, Target, 
  MessageCircle, Share2, Heart, Star, Send, User
} from 'lucide-react'
import { projects } from '../data/mockData'

const ProjectDetailPage = () => {
  const { id } = useParams()
  const [isJoined, setIsJoined] = useState(false)
  const [newComment, setNewComment] = useState('')
  const [rating, setRating] = useState(0)
  const [hoverRating, setHoverRating] = useState(0)

  const project = projects.find(p => p.id === parseInt(id))

  if (!project) {
    return (
      <div className="p-6 text-center">
        <h1 className="text-2xl font-bold text-gray-900 mb-4">Project not found</h1>
        <Link to="/projects" className="btn-primary">
          Back to Projects
        </Link>
      </div>
    )
  }

  const handleJoinProject = () => {
    setIsJoined(!isJoined)
  }

  const handleSubmitComment = (e) => {
    e.preventDefault()
    if (newComment.trim()) {
      // Here you would submit the comment to your backend
      console.log('Submitting comment:', newComment)
      setNewComment('')
      alert('Comment submitted successfully!')
    }
  }

  const getProgressColor = (progress) => {
    if (progress >= 80) return 'bg-green-500'
    if (progress >= 50) return 'bg-blue-500'
    if (progress >= 30) return 'bg-yellow-500'
    return 'bg-orange-500'
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <div className="relative h-96 overflow-hidden">
        <img
          src={project.image}
          alt={project.title}
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
            to="/projects"
            className="inline-flex items-center px-4 py-2 bg-white/90 backdrop-blur-sm rounded-lg text-gray-900 hover:bg-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4 mr-2" />
            Back to Projects
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
              <span className={`px-3 py-1 rounded-full text-xs font-medium ${
                project.status === 'active' 
                  ? 'bg-green-500/20 text-green-100 border border-green-400/30' 
                  : 'bg-gray-500/20 text-gray-100 border border-gray-400/30'
              }`}>
                {project.status}
              </span>
              <span className="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-xs font-medium border border-white/20">
                {project.category}
              </span>
            </div>
            <h1 className="text-4xl font-bold mb-4">{project.title}</h1>
            <div className="flex items-center space-x-6 text-white/90">
              <div className="flex items-center">
                <MapPin className="w-5 h-5 mr-2" />
                {project.location}
              </div>
              <div className="flex items-center">
                <Users className="w-5 h-5 mr-2" />
                {project.volunteers} volunteers
              </div>
              <div className="flex items-center">
                <TreePine className="w-5 h-5 mr-2" />
                {project.plantedTrees}/{project.targetTrees} trees
              </div>
            </div>
          </motion.div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto p-6 -mt-12 relative z-10">
        <div className="grid grid-cols-1 xl:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="xl:col-span-2 space-y-8">
            {/* Overview Card */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-8"
            >
              <h2 className="text-2xl font-bold text-gray-900 mb-6">Project Overview</h2>
              <p className="text-gray-600 text-lg leading-relaxed mb-8">
                {project.description}
              </p>

              {/* Progress Section */}
              <div className="mb-8">
                <div className="flex justify-between items-center mb-4">
                  <h3 className="text-xl font-semibold text-gray-900">Progress</h3>
                  <span className="text-2xl font-bold text-gray-900">{project.progress}%</span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-4 mb-4">
                  <motion.div
                    className={`h-4 rounded-full ${getProgressColor(project.progress)}`}
                    initial={{ width: 0 }}
                    animate={{ width: `${project.progress}%` }}
                    transition={{ delay: 0.5, duration: 1 }}
                  />
                </div>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div className="text-center">
                    <div className="text-2xl font-bold text-gray-900">{project.plantedTrees}</div>
                    <div className="text-sm text-gray-600">Trees Planted</div>
                  </div>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-gray-900">{project.targetTrees}</div>
                    <div className="text-sm text-gray-600">Target Trees</div>
                  </div>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-gray-900">{project.volunteers}</div>
                    <div className="text-sm text-gray-600">Volunteers</div>
                  </div>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-gray-900">{project.impact.co2Saved}</div>
                    <div className="text-sm text-gray-600">CO₂ Saved (kg)</div>
                  </div>
                </div>
              </div>

              {/* Impact */}
              <div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">Environmental Impact</h3>
                <div className="bg-green-50 rounded-lg p-6">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="flex items-center">
                      <Target className="w-8 h-8 text-green-600 mr-4" />
                      <div>
                        <div className="text-lg font-bold text-green-900">{project.impact.co2Saved} kg</div>
                        <div className="text-sm text-green-600">CO₂ Removed</div>
                      </div>
                    </div>
                    <div className="flex items-center">
                      <TreePine className="w-8 h-8 text-green-600 mr-4" />
                      <div>
                        <div className="text-lg font-bold text-green-900">{project.impact.areaGreened}</div>
                        <div className="text-sm text-green-600">Area Greened</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </motion.div>

            {/* Comments Section */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-8"
            >
              <h2 className="text-2xl font-bold text-gray-900 mb-6">Comments & Feedback</h2>

              {/* Add Comment */}
              <form onSubmit={handleSubmitComment} className="mb-8">
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Rate this project
                  </label>
                  <div className="flex space-x-1 mb-4">
                    {[1, 2, 3, 4, 5].map((star) => (
                      <button
                        key={star}
                        type="button"
                        onMouseEnter={() => setHoverRating(star)}
                        onMouseLeave={() => setHoverRating(0)}
                        onClick={() => setRating(star)}
                        className="text-2xl transition-colors"
                      >
                        <Star
                          className={`w-6 h-6 ${
                            star <= (hoverRating || rating)
                              ? 'text-yellow-400 fill-current'
                              : 'text-gray-300'
                          }`}
                        />
                      </button>
                    ))}
                  </div>
                </div>
                <textarea
                  value={newComment}
                  onChange={(e) => setNewComment(e.target.value)}
                  placeholder="Share your thoughts about this project..."
                  rows={4}
                  className="input-field mb-4"
                />
                <button type="submit" className="btn-primary">
                  <Send className="w-4 h-4 mr-2" />
                  Post Comment
                </button>
              </form>

              {/* Comments List */}
              <div className="space-y-6">
                {project.comments.map((comment) => (
                  <motion.div
                    key={comment.id}
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="flex space-x-4"
                  >
                    <img
                      src={comment.avatar}
                      alt={comment.user}
                      className="w-10 h-10 rounded-full"
                    />
                    <div className="flex-1">
                      <div className="bg-gray-50 rounded-lg p-4">
                        <div className="flex items-center justify-between mb-2">
                          <h4 className="font-medium text-gray-900">{comment.user}</h4>
                          <span className="text-sm text-gray-500">{comment.date}</span>
                        </div>
                        <p className="text-gray-600">{comment.comment}</p>
                      </div>
                    </div>
                  </motion.div>
                ))}
              </div>
            </motion.div>
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Join Project Card */}
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.3 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6"
            >
              <div className="text-center mb-6">
                <h3 className="text-xl font-bold text-gray-900 mb-2">
                  {isJoined ? 'You joined this project!' : 'Join This Project'}
                </h3>
                <p className="text-gray-600">
                  {isJoined 
                    ? 'Thank you for contributing to a greener future'
                    : 'Be part of the solution and help make our city greener'
                  }
                </p>
              </div>

              <motion.button
                onClick={handleJoinProject}
                whileHover={{ scale: 1.02 }}
                whileTap={{ scale: 0.98 }}
                className={`w-full py-3 px-6 rounded-lg font-medium transition-colors ${
                  isJoined
                    ? 'bg-green-100 text-green-700 border border-green-200'
                    : 'bg-primary-600 text-white hover:bg-primary-700'
                }`}
              >
                {isJoined ? (
                  <div className="flex items-center justify-center">
                    <Heart className="w-5 h-5 mr-2 fill-current" />
                    Joined
                  </div>
                ) : (
                  'Join Project'
                )}
              </motion.button>

              <div className="flex space-x-3 mt-4">
                <button className="flex-1 btn-ghost">
                  <Share2 className="w-4 h-4 mr-2" />
                  Share
                </button>
                <button className="flex-1 btn-ghost">
                  <MessageCircle className="w-4 h-4 mr-2" />
                  Contact
                </button>
              </div>
            </motion.div>

            {/* Project Details Card */}
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.4 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
              <h3 className="text-lg font-bold text-gray-900 mb-4">Project Details</h3>
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">Organizer</span>
                  <span className="font-medium text-gray-900">{project.organizer}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">Start Date</span>
                  <span className="font-medium text-gray-900">{project.startDate}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">End Date</span>
                  <span className="font-medium text-gray-900">{project.endDate}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">Category</span>
                  <span className="font-medium text-gray-900">{project.category}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">Status</span>
                  <span className={`font-medium capitalize ${
                    project.status === 'active' ? 'text-green-600' : 'text-gray-600'
                  }`}>
                    {project.status}
                  </span>
                </div>
              </div>
            </motion.div>

            {/* Related Projects */}
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.5 }}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
              <h3 className="text-lg font-bold text-gray-900 mb-4">Related Projects</h3>
              <div className="space-y-4">
                {projects.filter(p => p.id !== project.id && p.category === project.category).slice(0, 3).map((relatedProject) => (
                  <Link
                    key={relatedProject.id}
                    to={`/projects/${relatedProject.id}`}
                    className="block p-4 border border-gray-200 rounded-lg hover:border-primary-200 hover:bg-primary-50 transition-colors"
                  >
                    <div className="flex items-start space-x-3">
                      <img
                        src={relatedProject.image}
                        alt={relatedProject.title}
                        className="w-12 h-12 rounded-lg object-cover"
                      />
                      <div className="flex-1 min-w-0">
                        <h4 className="font-medium text-gray-900 truncate">
                          {relatedProject.title}
                        </h4>
                        <p className="text-sm text-gray-600 mt-1">
                          {relatedProject.volunteers} volunteers
                        </p>
                        <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
                          <div
                            className="bg-primary-500 h-2 rounded-full"
                            style={{ width: `${relatedProject.progress}%` }}
                          />
                        </div>
                      </div>
                    </div>
                  </Link>
                ))}
              </div>
            </motion.div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ProjectDetailPage