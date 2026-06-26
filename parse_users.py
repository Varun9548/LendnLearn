import urllib.request
import urllib.parse
import http.cookiejar
from html.parser import HTMLParser

# Create cookie jar
cookie_jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cookie_jar))
urllib.request.install_opener(opener)

# Admin login
login_url = "https://lendnlearn.onrender.com/login_verify.php"
admin_data = urllib.parse.urlencode({
    'login_type': 'admin',
    'email': 'admin@lendnlearn.local',
    'password': 'admin123'
}).encode('utf-8')

try:
    req = urllib.request.Request(login_url, data=admin_data, method='POST')
    with urllib.request.urlopen(req) as res:
        dashboard_content = res.read().decode('utf-8')
        
        # Simple extraction of the users table or rows
        print("--- Inspecting Deployed Users List ---")
        if "Manage Users" in dashboard_content:
            parts = dashboard_content.split("Manage Users")[1]
            if "<tbody>" in parts:
                tbody = parts.split("<tbody>")[1].split("</tbody>")[0]
                rows = tbody.split("<tr>")
                for row in rows:
                    if "tr>" in row or "td>" in row:
                        cols = row.split("<td>")
                        user_info = []
                        for col in cols[1:]:
                            val = col.split("</td>")[0].strip()
                            if "<span" in val:
                                val = val.split(">")[1].split("</span")[0].strip()
                            user_info.append(val)
                        if user_info:
                            print(" | ".join(user_info[:4])) # name, email, role, status
        else:
            print("Could not find Manage Users section.")
except Exception as e:
    print(f"Error fetching users: {e}")
