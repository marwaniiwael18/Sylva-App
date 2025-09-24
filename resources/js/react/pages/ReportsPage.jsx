/**
 * ReportsPage Component
 * Comprehensive page for managing environmental reports
 */

import React from 'react'
import { motion } from 'framer-motion'
import ReportsList from '../components/reports/ReportsList'

const ReportsPage = () => {
  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <div className="bg-gradient-to-r from-primary-600 to-green-600 rounded-2xl p-8 text-white">
            <div className="max-w-3xl">
              <h1 className="text-3xl font-bold mb-4">Environmental Reports</h1>
              <p className="text-lg text-green-100 leading-relaxed">
                View and manage community reports about environmental issues, maintenance needs, 
                and suggestions for green improvements. Help make your community more sustainable 
                and environmentally friendly.
              </p>
            </div>
          </div>
        </motion.div>

        {/* Reports List */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
        >
          <ReportsList 
            showCreateButton={true}
            showFilters={true}
          />
        </motion.div>
      </div>
    </div>
  )
}

export default ReportsPage