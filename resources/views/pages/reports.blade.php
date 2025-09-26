@extends('layouts.dashboard')

@section('page-title', 'Reports')
@section('page-subtitle', 'Manage environmental reports and track their status')

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Reports</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_reports'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="flag" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $statistics['pending_reports'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Validated</p>
                    <p class="text-2xl font-bold text-green-600">{{ $statistics['validated_reports'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $statistics['this_month'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Reports</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($report->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                {{ str_replace('_', ' ', $report->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $report->urgency === 'high' ? 'bg-red-100 text-red-800' : 
                                   ($report->urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($report->urgency) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $report->status === 'validated' ? 'bg-green-100 text-green-800' : 
                                   ($report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $report->user->name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('map') }}" class="text-green-600 hover:text-green-900 text-xs px-2 py-1 border border-green-600 rounded hover:bg-green-50 transition-colors duration-200">
                                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                    View on Map
                                </a>
                                <button onclick="editReportModal({{ $report->id }})" class="text-blue-600 hover:text-blue-900 text-xs px-2 py-1 border border-blue-600 rounded hover:bg-blue-50 transition-colors duration-200">
                                    <i data-lucide="edit-2" class="w-3 h-3 inline mr-1"></i>
                                    Edit
                                </button>
                                <button onclick="deleteReportConfirm({{ $report->id }})" class="text-red-600 hover:text-red-900 text-xs px-2 py-1 border border-red-600 rounded hover:bg-red-50 transition-colors duration-200">
                                    <i data-lucide="trash-2" class="w-3 h-3 inline mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i data-lucide="flag" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">No reports found</p>
                                <p class="text-sm">Be the first to report an environmental issue!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reports->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
        @endif
    </div>

    <!-- Edit Report Modal -->
    <div id="editReportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editReportForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Edit Report
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input type="text" id="editTitle" name="title" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea id="editDescription" name="description" required rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                            <select id="editType" name="type" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="tree_planting">Tree Planting</option>
                                                <option value="maintenance">Maintenance</option>
                                                <option value="pollution">Pollution</option>
                                                <option value="green_space_suggestion">Green Space</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Urgency</label>
                                            <select id="editUrgency" name="urgency" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="low">Low</option>
                                                <option value="medium">Medium</option>
                                                <option value="high">High</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Report
                        </button>
                        <button type="button" onclick="closeEditModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentEditingReportId = null;
const reports = @json($reports->items());

function editReportModal(reportId) {
    const report = reports.find(r => r.id == reportId);
    if (!report) {
        alert('Report not found');
        return;
    }
    
    currentEditingReportId = reportId;
    
    // Populate the form
    document.getElementById('editTitle').value = report.title;
    document.getElementById('editDescription').value = report.description;
    document.getElementById('editType').value = report.type;
    document.getElementById('editUrgency').value = report.urgency;
    
    // Show modal
    document.getElementById('editReportModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editReportModal').classList.add('hidden');
    currentEditingReportId = null;
}

async function deleteReportConfirm(reportId) {
    if (!confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/reports-public/${reportId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('Report deleted successfully!');
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            throw new Error(result.message || 'Failed to delete report');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting report: ' + error.message);
    }
}

// Handle edit form submission
document.getElementById('editReportForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentEditingReportId) {
        alert('No report selected for editing');
        return;
    }
    
    const formData = new FormData(this);
    const reportData = {
        title: formData.get('title'),
        description: formData.get('description'),
        type: formData.get('type'),
        urgency: formData.get('urgency')
    };
    
    try {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Updating...';
        submitBtn.disabled = true;

        const response = await fetch(`/api/reports-public/${currentEditingReportId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(reportData)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('Report updated successfully!');
            closeEditModal();
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            throw new Error(result.message || 'Failed to update report');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating report: ' + error.message);
    } finally {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.textContent = 'Update Report';
            submitBtn.disabled = false;
        }
    }
});

// Close modal when clicking outside
document.getElementById('editReportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endpush
@endsection