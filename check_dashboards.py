import urllib.request
import urllib.parse
import http.cookiejar

# Create a cookie jar to store session cookies
cookie_jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cookie_jar))
urllib.request.install_opener(opener)

print("--- Testing Admin Login & Dashboard ---")
# 1. Admin login
login_url = "https://lendnlearn.onrender.com/login_verify.php"
admin_data = urllib.parse.urlencode({
    'login_type': 'admin',
    'email': 'admin@lendnlearn.local',
    'password': 'admin123'
}).encode('utf-8')

try:
    req = urllib.request.Request(login_url, data=admin_data, method='POST')
    with urllib.request.urlopen(req) as res:
        final_url = res.geturl()
        print(f"Logged in. Redirected to: {final_url}")
        
        # Read dashboard page content
        dashboard_content = res.read().decode('utf-8')
        if "LendnLearn Admin" in dashboard_content or "Manage Uploaded Books" in dashboard_content:
            print("SUCCESS: Admin Dashboard loaded correctly!")
        else:
            print("FAILURE: Admin Dashboard text not found in response.")
            print(dashboard_content[:500])
except Exception as e:
    print(f"Admin login failed: {e}")


print("\n--- Testing Regular User Login & Home ---")
# Reset cookie jar for fresh login
cookie_jar.clear()

user_data = urllib.parse.urlencode({
    'login_type': 'user',
    'email': 'demo@lendnlearn.local',
    'password': 'demo123'
}).encode('utf-8')

try:
    req = urllib.request.Request(login_url, data=user_data, method='POST')
    with urllib.request.urlopen(req) as res:
        final_url = res.geturl()
        print(f"Logged in. Redirected to: {final_url}")
        
        home_content = res.read().decode('utf-8')
        if "Recently Added Books" in home_content or "Hello," in home_content:
            print("SUCCESS: Home page loaded correctly!")
        else:
            print("FAILURE: Home page text not found in response.")
            print(home_content[:500])
except Exception as e:
    print(f"User login failed: {e}")
