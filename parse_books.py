import urllib.request
import urllib.parse
import http.cookiejar

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
        
        print("--- Inspecting Deployed Books List ---")
        if "Manage Uploaded Books" in dashboard_content:
            parts = dashboard_content.split("Manage Uploaded Books")[1]
            if "<tbody>" in parts:
                tbody = parts.split("<tbody>")[1].split("</tbody>")[0]
                rows = tbody.split("<tr>")
                for row in rows:
                    if "tr>" in row or "td>" in row:
                        cols = row.split("<td>")
                        book_info = []
                        for col in cols[1:]:
                            val = col.split("</td>")[0].strip()
                            if "<form" in val:
                                # Try to extract the price value from the input field
                                if 'name="book_price"' in val:
                                    price_val = val.split('value="')[1].split('"')[0]
                                    val = f"${price_val} (Editable)"
                                else:
                                    val = "Form/Button"
                            book_info.append(val)
                        if book_info:
                            print(" | ".join(book_info[:7]))
        else:
            print("Could not find Manage Uploaded Books section.")
except Exception as e:
    print(f"Error fetching books: {e}")
