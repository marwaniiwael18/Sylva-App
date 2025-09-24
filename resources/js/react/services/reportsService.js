import { apiClient } from './api'

/**
 * Reports API Service
 * Handles all report-related API calls to Laravel backend
 */
export class ReportsService {
  /**
   * Get all reports with optional filters
   */
  static async getAllReports(filters = {}) {
    try {
      const params = new URLSearchParams()
      
      // Add filters to query params
      if (filters.status) params.append('status', filters.status)
      if (filters.type) params.append('type', filters.type)
      if (filters.urgency) params.append('urgency', filters.urgency)
      if (filters.search) params.append('search', filters.search)
      if (filters.latitude && filters.longitude) {
        params.append('latitude', filters.latitude)
        params.append('longitude', filters.longitude)
        if (filters.radius) params.append('radius', filters.radius)
      }
      
      const queryString = params.toString()
      const url = queryString ? `/reports-public?${queryString}` : '/reports-public'
      
      const response = await apiClient.get(url)
      return {
        success: true,
        data: response.data.data,
        pagination: response.data.pagination
      }
    } catch (error) {
      console.error('Error fetching reports:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors du chargement des signalements',
        data: [],
        pagination: null
      }
    }
  }

  /**
   * Get a specific report by ID
   */
  static async getReport(id) {
    try {
      const response = await apiClient.get(`/reports-public/${id}`)
      return {
        success: true,
        data: response.data.data
      }
    } catch (error) {
      console.error('Error fetching report:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors du chargement du signalement',
        data: null
      }
    }
  }

  /**
   * Create a new report
   */
  static async createReport(reportData) {
    try {
      const formData = new FormData()
      
      // Add text fields
      formData.append('title', reportData.title)
      formData.append('description', reportData.description)
      formData.append('type', reportData.type)
      formData.append('urgency', reportData.urgency)
      formData.append('latitude', reportData.latitude)
      formData.append('longitude', reportData.longitude)
      if (reportData.address) formData.append('address', reportData.address)
      
      // Add images if any
      if (reportData.images && reportData.images.length > 0) {
        reportData.images.forEach((image, index) => {
          formData.append(`images[${index}]`, image)
        })
      }
      
      const response = await apiClient.post('/reports-public', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error creating report:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors de la création du signalement',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Update an existing report
   */
  static async updateReport(id, reportData) {
    try {
      const formData = new FormData()
      formData.append('_method', 'PUT')
      
      // Add text fields that are being updated
      Object.keys(reportData).forEach(key => {
        if (key === 'images' && reportData[key] && reportData[key].length > 0) {
          reportData[key].forEach((image, index) => {
            formData.append(`images[${index}]`, image)
          })
        } else if (reportData[key] !== null && reportData[key] !== undefined) {
          formData.append(key, reportData[key])
        }
      })
      
      const response = await apiClient.post(`/reports-public/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error updating report:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors de la mise à jour du signalement',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Delete a report
   */
  static async deleteReport(id) {
    try {
      const response = await apiClient.delete(`/reports-public/${id}`)
      return {
        success: true,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error deleting report:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors de la suppression du signalement'
      }
    }
  }

  /**
   * Validate a report (admin/moderator only)
   */
  static async validateReport(id, validationData) {
    try {
      const response = await apiClient.post(`/reports-public/${id}/validate`, validationData)
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error validating report:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors de la validation du signalement'
      }
    }
  }

  /**
   * Get reports statistics
   */
  static async getStatistics() {
    try {
      const response = await apiClient.get('/reports-statistics-public')
      return {
        success: true,
        data: response.data.data
      }
    } catch (error) {
      console.error('Error fetching statistics:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors du chargement des statistiques',
        data: null
      }
    }
  }

  /**
   * Get reports near a location
   */
  static async getReportsNear(latitude, longitude, radius = 10) {
    return this.getAllReports({
      latitude,
      longitude,
      radius
    })
  }

  /**
   * Get user's own reports
   */
  static async getUserReports(userId) {
    return this.getAllReports({ user_id: userId })
  }

  /**
   * Get pending reports for validation
   */
  static async getPendingReports() {
    return this.getAllReports({ status: 'pending' })
  }
}

/**
 * Report Types Configuration
 */
export const REPORT_TYPES = {
  tree_planting: {
    label: 'Plantation d\'arbres',
    icon: '🌳',
    color: 'green',
    description: 'Suggérer un emplacement pour planter des arbres'
  },
  maintenance: {
    label: 'Entretien nécessaire',
    icon: '🔧',
    color: 'orange',
    description: 'Signaler un besoin d\'entretien d\'espace vert'
  },
  pollution: {
    label: 'Pollution constatée',
    icon: '⚠️',
    color: 'red',
    description: 'Signaler une pollution environnementale'
  },
  green_space_suggestion: {
    label: 'Nouvel espace vert',
    icon: '🌱',
    color: 'blue',
    description: 'Proposer la création d\'un espace vert'
  }
}

/**
 * Report Urgency Levels
 */
export const URGENCY_LEVELS = {
  low: {
    label: 'Faible',
    color: 'green',
    priority: 1
  },
  medium: {
    label: 'Moyenne',
    color: 'orange',
    priority: 2
  },
  high: {
    label: 'Élevée',
    color: 'red',
    priority: 3
  }
}

/**
 * Report Status
 */
export const REPORT_STATUS = {
  pending: {
    label: 'En attente',
    color: 'yellow',
    description: 'En attente de validation'
  },
  validated: {
    label: 'Validé',
    color: 'blue',
    description: 'Validé par l\'administration'
  },
  in_progress: {
    label: 'En cours',
    color: 'orange',
    description: 'Traitement en cours'
  },
  completed: {
    label: 'Terminé',
    color: 'green',
    description: 'Problème résolu'
  },
  rejected: {
    label: 'Rejeté',
    color: 'red',
    description: 'Signalement rejeté'
  }
}

export default ReportsService