from datetime import datetime


def time_ago(dt):
    now = datetime.utcnow()
    diff = now - dt

    # Convert the difference to seconds
    diff_seconds = int(diff.total_seconds())

    if diff_seconds < 60:
        return "just now"
    elif diff_seconds < 60 * 60:
        minutes = diff_seconds // 60
        return f"{minutes} minute{'s' if minutes > 1 else ''} ago"
    elif diff_seconds < 60 * 60 * 24:
        hours = diff_seconds // (60 * 60)
        return f"{hours} hour{'s' if hours > 1 else ''} ago"
    elif diff_seconds < 60 * 60 * 24 * 30:
        days = diff_seconds // (60 * 60 * 24)
        return f"{days} day{'s' if days > 1 else ''} ago"
    elif diff_seconds < 60 * 60 * 24 * 365:
        months = diff_seconds // (60 * 60 * 24 * 30)
        return f"{months} month{'s' if months > 1 else ''} ago"
    else:
        years = diff_seconds // (60 * 60 * 24 * 365)
        return f"{years} year{'s' if years > 1 else ''} ago"

def print_error(message):
    print(f"\033[91m{message}\033[0m")

def print_warning(message):
    print(f"\033[93m{message}\033[0m")
