import React, { useState } from 'react'
import { motion } from 'framer-motion'
import { 
  TreePine, 
  Users, 
  Calendar, 
  Leaf, 
  Trophy, 
  TrendingUp,
  Award,
  Star,
  MapPin,
  Clock,
  Target,
  Zap,
  Heart
} from 'lucide-react'
import { 
  AreaChart, 
  Area, 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
  LineChart,
  Line
} from 'recharts'
import { impactData } from '../data/mockData'

const ImpactPage = () => {
  const [timeFilter, setTimeFilter] = useState('6months')
  
  const badges = [
    {
      id: 1,
      name: "Tree Planter",
      description: "Planted 50+ trees",
      icon: TreePine,
      color: "bg-green-500",
      unlocked: true,
      progress: 100,
      total: 50
    },
    {
      id: 2,
      name: "Community Leader",
      description: "Organized 10+ events",
      icon: Users,
      color: "bg-blue-500",
      unlocked: true,
      progress: 100,
      total: 10
    },
    {
      id: 3,
      name: "Eco Warrior",
      description: "200 hours volunteering",
      icon: Leaf,
      color: "bg-emerald-500",
      unlocked: true,
      progress: 85,
      total: 200
    },
    {
      id: 4,
      name: "Impact Champion",
      description: "1000+ CO₂ saved",
      icon: Trophy,
      color: "bg-yellow-500",
      unlocked: false,
      progress: 65,
      total: 1000
    },
    {
      id: 5,
      name: "Green Mentor",
      description: "Mentored 25+ volunteers",
      icon: Award,
      color: "bg-purple-500",
      unlocked: false,
      progress: 40,
      total: 25
    },
    {
      id: 6,
      name: "Sustainability Star",
      description: "1 year active member",
      icon: Star,
      color: "bg-orange-500",
      unlocked: false,
      progress: 75,
      total: 365
    }
  ]

  const monthlyData = [
    { month: 'Jan', trees: 45, events: 8, volunteers: 120 },
    { month: 'Feb', trees: 52, events: 12, volunteers: 145 },
    { month: 'Mar', trees: 78, events: 15, volunteers: 180 },
    { month: 'Apr', trees: 95, events: 18, volunteers: 220 },
    { month: 'May', trees: 120, events: 22, volunteers: 280 },
    { month: 'Jun', trees: 85, events: 16, volunteers: 190 }
  ]

  const co2Data = [
    { name: 'Trees Planted', value: 45, color: '#10B981' },
    { name: 'Waste Reduced', value: 30, color: '#3B82F6' },
    { name: 'Water Saved', value: 15, color: '#6366F1' },
    { name: 'Energy Saved', value: 10, color: '#8B5CF6' }
  ]

  const personalStats = [
    { label: 'Trees Planted', value: 127, icon: TreePine, color: 'text-green-600' },
    { label: 'Events Attended', value: 23, icon: Calendar, color: 'text-blue-600' },
    { label: 'Hours Volunteered', value: 186, icon: Clock, color: 'text-purple-600' },
    { label: 'CO₂ Offset (kg)', value: 847, icon: Leaf, color: 'text-emerald-600' }
  ]

  const communityStats = [
    { label: 'Active Members', value: '2,847', change: '+12%', trend: 'up' },
    { label: 'Projects Completed', value: '156', change: '+8%', trend: 'up' },
    { label: 'Trees This Month', value: '1,248', change: '+23%', trend: 'up' },
    { label: 'CO₂ Offset (tons)', value: '42.3', change: '+15%', trend: 'up' }
  ]

  const achievements = [
    {
      title: "First Tree Planted",
      date: "March 15, 2024",
      description: "Planted your first tree in Central Park",
      icon: TreePine
    },
    {
      title: "Event Organizer",
      date: "April 2, 2024", 
      description: "Successfully organized your first community event",
      icon: Users
    },
    {
      title: "100 Trees Milestone",
      date: "May 18, 2024",
      description: "Reached 100 trees planted - amazing work!",
      icon: Trophy
    }
  ]

  return (
    <div className="p-6 space-y-8">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
      >
        <h1 className="text-3xl font-bold text-gray-900 mb-2">Impact Dashboard</h1>
        <p className="text-gray-600">
          Track your environmental impact and celebrate achievements
        </p>
      </motion.div>

      {/* Personal Stats */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        {personalStats.map((stat, index) => (
          <div key={stat.label} className="bg-white rounded-xl border border-gray-200 p-6">
            <div className="flex items-center justify-between mb-4">
              <div className={`p-3 rounded-lg bg-opacity-10 ${
                stat.color.includes('green') ? 'bg-green-100' :
                stat.color.includes('blue') ? 'bg-blue-100' :
                stat.color.includes('purple') ? 'bg-purple-100' :
                'bg-emerald-100'
              }`}>
                <stat.icon className={`w-6 h-6 ${stat.color}`} />
              </div>
              <TrendingUp className="w-5 h-5 text-green-500" />
            </div>
            <div className="text-2xl font-bold text-gray-900 mb-1">
              {stat.value.toLocaleString()}
            </div>
            <div className="text-sm text-gray-600">{stat.label}</div>
          </div>
        ))}
      </motion.div>

      {/* Charts Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Monthly Activity */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="bg-white rounded-xl border border-gray-200 p-6"
        >
          <div className="flex items-center justify-between mb-6">
            <h3 className="text-xl font-bold text-gray-900">Monthly Activity</h3>
            <select
              value={timeFilter}
              onChange={(e) => setTimeFilter(e.target.value)}
              className="input-field text-sm"
            >
              <option value="3months">Last 3 months</option>
              <option value="6months">Last 6 months</option>
              <option value="1year">Last year</option>
            </select>
          </div>
          
          <ResponsiveContainer width="100%" height={300}>
            <AreaChart data={monthlyData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip />
              <Area 
                type="monotone" 
                dataKey="trees" 
                stackId="1"
                stroke="#10B981" 
                fill="#10B981" 
                fillOpacity={0.6}
                name="Trees Planted"
              />
              <Area 
                type="monotone" 
                dataKey="volunteers" 
                stackId="1"
                stroke="#3B82F6" 
                fill="#3B82F6" 
                fillOpacity={0.6}
                name="Volunteers"
              />
            </AreaChart>
          </ResponsiveContainer>
        </motion.div>

        {/* CO2 Impact */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="bg-white rounded-xl border border-gray-200 p-6"
        >
          <h3 className="text-xl font-bold text-gray-900 mb-6">CO₂ Impact Breakdown</h3>
          
          <ResponsiveContainer width="100%" height={300}>
            <PieChart>
              <Pie
                data={co2Data}
                cx="50%"
                cy="50%"
                outerRadius={100}
                fill="#8884d8"
                dataKey="value"
                label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
              >
                {co2Data.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={entry.color} />
                ))}
              </Pie>
              <Tooltip />
            </PieChart>
          </ResponsiveContainer>
        </motion.div>
      </div>

      {/* Community Impact */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.4 }}
        className="bg-white rounded-xl border border-gray-200 p-6"
      >
        <h3 className="text-xl font-bold text-gray-900 mb-6">Community Impact</h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {communityStats.map((stat, index) => (
            <div key={stat.label} className="text-center">
              <div className="text-3xl font-bold text-gray-900 mb-1">
                {stat.value}
              </div>
              <div className="text-sm text-gray-600 mb-2">{stat.label}</div>
              <div className={`text-sm flex items-center justify-center ${
                stat.trend === 'up' ? 'text-green-600' : 'text-red-600'
              }`}>
                <TrendingUp className="w-4 h-4 mr-1" />
                {stat.change}
              </div>
            </div>
          ))}
        </div>

        <ResponsiveContainer width="100%" height={200}>
          <LineChart data={monthlyData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="month" />
            <YAxis />
            <Tooltip />
            <Line 
              type="monotone" 
              dataKey="trees" 
              stroke="#10B981" 
              strokeWidth={3}
              name="Trees Planted"
            />
          </LineChart>
        </ResponsiveContainer>
      </motion.div>

      {/* Badges & Achievements */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Badges */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.5 }}
          className="bg-white rounded-xl border border-gray-200 p-6"
        >
          <h3 className="text-xl font-bold text-gray-900 mb-6">Badges & Milestones</h3>
          
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {badges.map((badge, index) => (
              <motion.div
                key={badge.id}
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ delay: 0.1 * index }}
                className={`relative p-4 rounded-xl border-2 transition-all ${
                  badge.unlocked 
                    ? 'border-green-200 bg-green-50' 
                    : 'border-gray-200 bg-gray-50'
                }`}
              >
                <div className="flex items-start space-x-3">
                  <div className={`p-2 rounded-lg ${badge.color} ${
                    badge.unlocked ? 'opacity-100' : 'opacity-50'
                  }`}>
                    <badge.icon className="w-5 h-5 text-white" />
                  </div>
                  
                  <div className="flex-1">
                    <h4 className={`font-semibold ${
                      badge.unlocked ? 'text-gray-900' : 'text-gray-500'
                    }`}>
                      {badge.name}
                    </h4>
                    <p className={`text-sm ${
                      badge.unlocked ? 'text-gray-600' : 'text-gray-400'
                    }`}>
                      {badge.description}
                    </p>
                    
                    {!badge.unlocked && (
                      <div className="mt-2">
                        <div className="flex justify-between text-xs text-gray-500 mb-1">
                          <span>Progress</span>
                          <span>{badge.progress}%</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                          <div
                            className="bg-primary-600 h-2 rounded-full transition-all duration-500"
                            style={{ width: `${badge.progress}%` }}
                          />
                        </div>
                      </div>
                    )}
                  </div>
                </div>
                
                {badge.unlocked && (
                  <div className="absolute top-2 right-2">
                    <div className="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                      <Trophy className="w-3 h-3 text-white" />
                    </div>
                  </div>
                )}
              </motion.div>
            ))}
          </div>
        </motion.div>

        {/* Recent Achievements */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.6 }}
          className="bg-white rounded-xl border border-gray-200 p-6"
        >
          <h3 className="text-xl font-bold text-gray-900 mb-6">Recent Achievements</h3>
          
          <div className="space-y-4">
            {achievements.map((achievement, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ delay: 0.1 * index }}
                className="flex items-start space-x-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
              >
                <div className="p-2 bg-yellow-100 rounded-lg">
                  <achievement.icon className="w-5 h-5 text-yellow-600" />
                </div>
                
                <div className="flex-1">
                  <h4 className="font-semibold text-gray-900 mb-1">
                    {achievement.title}
                  </h4>
                  <p className="text-sm text-gray-600 mb-2">
                    {achievement.description}
                  </p>
                  <p className="text-xs text-gray-500">
                    {achievement.date}
                  </p>
                </div>
                
                <div className="p-1 bg-yellow-200 rounded-full">
                  <Trophy className="w-4 h-4 text-yellow-600" />
                </div>
              </motion.div>
            ))}
          </div>
          
          <motion.button
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            className="w-full mt-6 py-3 px-4 bg-gradient-to-r from-primary-600 to-green-600 text-white rounded-lg font-medium hover:from-primary-700 hover:to-green-700 transition-all duration-200"
          >
            View All Achievements
          </motion.button>
        </motion.div>
      </div>

      {/* Goals & Targets */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.7 }}
        className="bg-white rounded-xl border border-gray-200 p-6"
      >
        <div className="flex items-center justify-between mb-6">
          <h3 className="text-xl font-bold text-gray-900">2024 Goals</h3>
          <button className="text-sm text-primary-600 hover:text-primary-700 font-medium">
            Set New Goal
          </button>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {[
            { label: 'Trees to Plant', current: 127, target: 200, icon: TreePine },
            { label: 'Events to Attend', current: 23, target: 36, icon: Calendar },
            { label: 'CO₂ to Offset (kg)', current: 847, target: 1200, icon: Leaf }
          ].map((goal, index) => (
            <div key={goal.label} className="text-center">
              <div className="flex justify-center mb-4">
                <div className="relative w-24 h-24">
                  <svg className="w-24 h-24 transform -rotate-90" viewBox="0 0 100 100">
                    <circle
                      cx="50"
                      cy="50"
                      r="40"
                      stroke="#e5e7eb"
                      strokeWidth="8"
                      fill="none"
                    />
                    <circle
                      cx="50"
                      cy="50"
                      r="40"
                      stroke="#10B981"
                      strokeWidth="8"
                      fill="none"
                      strokeDasharray={`${2 * Math.PI * 40}`}
                      strokeDashoffset={`${2 * Math.PI * 40 * (1 - goal.current / goal.target)}`}
                      className="transition-all duration-500"
                    />
                  </svg>
                  <div className="absolute inset-0 flex items-center justify-center">
                    <goal.icon className="w-6 h-6 text-primary-600" />
                  </div>
                </div>
              </div>
              
              <h4 className="font-semibold text-gray-900 mb-2">{goal.label}</h4>
              <p className="text-2xl font-bold text-gray-900 mb-1">
                {goal.current}
                <span className="text-lg text-gray-500 font-normal">
                  /{goal.target}
                </span>
              </p>
              <p className="text-sm text-gray-600">
                {Math.round((goal.current / goal.target) * 100)}% Complete
              </p>
            </div>
          ))}
        </div>
      </motion.div>
    </div>
  )
}

export default ImpactPage