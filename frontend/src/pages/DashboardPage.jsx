import React from 'react'
import { motion } from 'framer-motion'
import { TreePine, Users, Calendar, Target, TrendingUp, Award, MapPin, Clock } from 'lucide-react'
import { useAuth } from '../contexts/AuthContext'
import { recentActivities } from '../data/mockData'

const DashboardPage = () => {
  const { user } = useAuth()

  const stats = [
    {
      title: 'Trees Planted',
      value: user?.stats?.treesPlanted || 0,
      icon: TreePine,
      color: 'text-green-600',
      bgColor: 'bg-green-50',
      change: '+12 this month'
    },
    {
      title: 'Projects Joined',
      value: user?.stats?.projectsJoined || 0,
      icon: Users,
      color: 'text-blue-600',
      bgColor: 'bg-blue-50',
      change: '+2 this month'
    },
    {
      title: 'Events Attended',
      value: user?.stats?.eventsAttended || 0,
      icon: Calendar,
      color: 'text-purple-600',
      bgColor: 'bg-purple-50',
      change: '+3 this month'
    },
    {
      title: 'CO₂ Saved (kg)',
      value: user?.stats?.co2Saved || 0,
      icon: Target,
      color: 'text-emerald-600',
      bgColor: 'bg-emerald-50',
      change: '+45kg this month'
    }
  ]

  const quickActions = [
    {
      title: 'Join a Project',
      description: 'Find and join ongoing green projects in your area',
      icon: TreePine,
      color: 'bg-green-600',
      href: '/projects'
    },
    {
      title: 'Attend an Event',
      description: 'Discover upcoming environmental events',
      icon: Calendar,
      color: 'bg-blue-600',
      href: '/events'
    },
    {
      title: 'Report an Issue',
      description: 'Help identify areas that need greening',
      icon: MapPin,
      color: 'bg-orange-600',
      href: '/map'
    }
  ]

  return (
    <div className="p-6 space-y-8">
      {/* Welcome Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="text-center md:text-left"
      >
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          Welcome back, {user?.name?.split(' ')[0] || 'there'}! 👋
        </h1>
        <p className="text-gray-600">
          Ready to make a positive impact on the environment today?
        </p>
      </motion.div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        {stats.map((stat, index) => {
          const IconComponent = stat.icon
          return (
            <motion.div
              key={stat.title}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.1 }}
              className="card p-6 hover:shadow-lg transition-all duration-300"
            >
              <div className="flex items-center justify-between mb-4">
                <div className={`w-12 h-12 ${stat.bgColor} rounded-xl flex items-center justify-center`}>
                  <IconComponent className={`w-6 h-6 ${stat.color}`} />
                </div>
                <div className="text-right">
                  <div className="text-2xl font-bold text-gray-900">{stat.value}</div>
                  <div className="text-sm text-gray-500">{stat.title}</div>
                </div>
              </div>
              <div className="flex items-center text-sm text-green-600">
                <TrendingUp className="w-4 h-4 mr-1" />
                {stat.change}
              </div>
            </motion.div>
          )
        })}
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-3 gap-8">
        {/* Quick Actions */}
        <motion.div
          initial={{ opacity: 0, x: -20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 0.4 }}
          className="xl:col-span-2"
        >
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {quickActions.map((action, index) => {
              const IconComponent = action.icon
              return (
                <motion.a
                  key={action.title}
                  href={action.href}
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                  className="card p-6 hover:shadow-lg transition-all duration-300 cursor-pointer group"
                >
                  <div className={`w-12 h-12 ${action.color} rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
                    <IconComponent className="w-6 h-6 text-white" />
                  </div>
                  <h3 className="font-semibold text-gray-900 mb-2">{action.title}</h3>
                  <p className="text-sm text-gray-600">{action.description}</p>
                </motion.a>
              )
            })}
          </div>
        </motion.div>

        {/* Recent Activity */}
        <motion.div
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 0.6 }}
        >
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Recent Activity</h2>
          <div className="card p-6">
            <div className="space-y-4">
              {recentActivities.slice(0, 5).map((activity, index) => (
                <motion.div
                  key={activity.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.8 + index * 0.1 }}
                  className="flex items-start space-x-3"
                >
                  <div className="w-8 h-8 bg-primary-50 rounded-full flex items-center justify-center flex-shrink-0">
                    <span className="text-sm">{activity.icon}</span>
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm text-gray-900">{activity.message}</p>
                    <div className="flex items-center text-xs text-gray-500 mt-1">
                      <Clock className="w-3 h-3 mr-1" />
                      {activity.timestamp}
                    </div>
                  </div>
                </motion.div>
              ))}
            </div>
            <motion.button
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
              className="w-full mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium"
            >
              View all activity
            </motion.button>
          </div>
        </motion.div>
      </div>

      {/* Badges Section */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.8 }}
      >
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Your Badges</h2>
        <div className="card p-6">
          <div className="flex flex-wrap gap-4">
            {user?.badges?.map((badge, index) => (
              <motion.div
                key={badge}
                initial={{ opacity: 0, scale: 0 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ delay: 1 + index * 0.1 }}
                className="flex items-center space-x-2 bg-primary-50 text-primary-700 px-4 py-2 rounded-full"
              >
                <Award className="w-4 h-4" />
                <span className="text-sm font-medium">{badge}</span>
              </motion.div>
            )) || (
              <div className="text-gray-500 text-center w-full py-8">
                <Award className="w-12 h-12 mx-auto mb-4 text-gray-300" />
                <p>Start participating in projects to earn badges!</p>
              </div>
            )}
          </div>
        </div>
      </motion.div>

      {/* Progress Section */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 1 }}
      >
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Impact Progress</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Trees Progress */}
          <div className="card p-6">
            <div className="flex items-center justify-between mb-4">
              <h3 className="font-semibold text-gray-900">Trees to Next Badge</h3>
              <span className="text-sm text-gray-500">{user?.stats?.treesPlanted || 0}/50</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-3">
              <motion.div
                className="bg-green-500 h-3 rounded-full"
                initial={{ width: 0 }}
                animate={{ width: `${((user?.stats?.treesPlanted || 0) / 50) * 100}%` }}
                transition={{ delay: 1.2, duration: 1 }}
              />
            </div>
            <p className="text-sm text-gray-600 mt-2">
              {50 - (user?.stats?.treesPlanted || 0)} more trees to unlock "Master Planter" badge
            </p>
          </div>

          {/* CO2 Progress */}
          <div className="card p-6">
            <div className="flex items-center justify-between mb-4">
              <h3 className="font-semibold text-gray-900">CO₂ Impact Goal</h3>
              <span className="text-sm text-gray-500">{user?.stats?.co2Saved || 0}/500 kg</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-3">
              <motion.div
                className="bg-blue-500 h-3 rounded-full"
                initial={{ width: 0 }}
                animate={{ width: `${((user?.stats?.co2Saved || 0) / 500) * 100}%` }}
                transition={{ delay: 1.4, duration: 1 }}
              />
            </div>
            <p className="text-sm text-gray-600 mt-2">
              {500 - (user?.stats?.co2Saved || 0)} kg more to reach your monthly goal
            </p>
          </div>
        </div>
      </motion.div>
    </div>
  )
}

export default DashboardPage