@extends('layouts.app')

@section('title', 'Course Management')

@section('content')
<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Course Management</h1>
        <p style="color: #64748b; font-size: 1rem;">Browse and manage course offerings</p>
    </div>

    <!-- Filters -->
    <div class="filters-card" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
        <form method="GET" action="{{ route('courses.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Course code or title..." 
                    style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
            </div>
            
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">School</label>
                <select name="school_id" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Year Level</label>
                <select name="year_level" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                    <option value="">All Years</option>
                    @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ request('year_level') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                    @endfor
                </select>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="flex: 1; background: #2563eb; color: white; padding: 0.625rem 1.25rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Filter
                </button>
                <a href="{{ route('courses.index') }}" style="flex: 1; background: #f1f5f9; color: #475569; padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Courses Grid -->
    <div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @forelse($courses as $course)
            <div class="course-card" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">{{ $course->course_code }}</h3>
                        <p style="color: #64748b; font-size: 0.875rem;">{{ $course->school->name }}</p>
                    </div>
                    <span style="background: {{ $course->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $course->is_active ? '#166534' : '#991b1b' }}; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                        {{ $course->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <h4 style="font-size: 0.9375rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem;">{{ $course->title }}</h4>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">Units</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">{{ $course->units }}</div>
                    </div>
                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">Year Level</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">{{ $course->year_level }}</div>
                    </div>
                </div>
                
                @if($course->description)
                    <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">
                        {{ Str::limit($course->description, 100) }}
                    </p>
                @endif
                
                <a href="{{ route('courses.show', $course->id) }}" style="display: block; text-align: center; background: #eff6ff; color: #2563eb; padding: 0.625rem; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.875rem;">
                    View Details
                </a>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                <svg style="width: 64px; height: 64px; color: #cbd5e1; margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 style="font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">No courses found</h3>
                <p style="color: #64748b;">Try adjusting your filters</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
        <div style="display: flex; justify-content: center;">
            {{ $courses->links() }}
        </div>
    @endif
</div>

<style>
.course-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}
</style>
@endsection
