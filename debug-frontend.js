// Frontend CRUD Operations Debug Script
// Run this in the browser console to test the API calls

console.log('🔧 Testing Frontend CRUD Operations...')

// Test the ReportsService directly
const testFrontendCRUD = async () => {
  try {
    // Import the service (you may need to adapt this based on your module system)
    const { ReportsService } = await import('./resources/js/react/services/reportsService.js')
    
    console.log('📡 Testing GET all reports...')
    const reports = await ReportsService.getAllReports()
    console.log('✅ GET Success:', reports)
    
    if (reports.success && reports.data.length > 0) {
      const reportId = reports.data[0].id
      
      console.log(`📝 Testing UPDATE report ${reportId}...`)
      const updateResult = await ReportsService.updateReport(reportId, {
        title: 'Frontend Test Update',
        description: 'Updated via frontend debug script'
      })
      console.log('✅ UPDATE Result:', updateResult)
      
      console.log(`🗑️ Testing DELETE report ${reportId}...`)
      const deleteResult = await ReportsService.deleteReport(reportId)
      console.log('✅ DELETE Result:', deleteResult)
    }
    
  } catch (error) {
    console.error('❌ Frontend test error:', error)
  }
}

// Test with direct axios calls
const testDirectAPI = async () => {
  try {
    console.log('📡 Testing direct API calls...')
    
    // GET request
    const getResponse = await fetch('/api/reports-public')
    const getResult = await getResponse.json()
    console.log('✅ Direct GET:', getResult)
    
    if (getResult.success && getResult.data.length > 0) {
      const reportId = getResult.data[0].id
      
      // UPDATE request
      const updateResponse = await fetch(`/api/reports-public/${reportId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          title: 'Direct API Test Update',
          description: 'Updated via direct fetch call'
        })
      })
      const updateResult = await updateResponse.json()
      console.log('✅ Direct UPDATE:', updateResult)
      
      // DELETE request
      const deleteResponse = await fetch(`/api/reports-public/${reportId}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      const deleteResult = await deleteResponse.json()
      console.log('✅ Direct DELETE:', deleteResult)
    }
    
  } catch (error) {
    console.error('❌ Direct API test error:', error)
  }
}

// Run both tests
testDirectAPI()
// testFrontendCRUD() // Uncomment if modules are available