import java.util.Properties;
import java.io.*;
import javax.servlet.*;
import javax.servlet.http.*;

public class FGReportServlet extends HttpServlet{
	private FGTelnetConnection conn = null;

	public void init(ServletConfig servletConfig)
		throws ServletException {

		super.init(servletConfig);

		try {
			//Load configuration file
			String configFilePath = servletConfig.getServletContext().getRealPath("/WEB-INF") + "/fgfs.conf";
			Properties conf = new Properties();
			FileInputStream in = new FileInputStream(configFilePath);
			conf.load(in);
			in.close();

			//Initialize connection
			conn = new FGTelnetConnection(
				conf.getProperty("FGFS_TELNET_ADDR"),
				Integer.parseInt(conf.getProperty("FGFS_TELNET_BASE_PORT"))
			);
		} catch (IOException e) {
			throw new ServletException(e.toString());
		}
	}

	public void destroy() {
		try {
			this.conn.close();
		} catch (IOException e) {
			System.out.println(e.toString());
		}
	}

	public void doPost(HttpServletRequest request, HttpServletResponse response)
		throws IOException, ServletException {

		response.setContentType("text/plain;charset=UTF-8");
		PrintWriter out = response.getWriter();

		out.println("Real-world Time:           "
			+ this.conn.get("/sim/time/real/string"));
		out.println("UTC Time:                  "
			+ this.conn.get("/sim/time/utc/string"));
		out.println("Local Time:                "
			+ this.conn.get("/instrumentation/clock/local-short-string"));
		out.println("Aircraft:                  "
			+ this.conn.get("/sim/description"));
		out.printf ("Position (lat lon):        %.6f %.6f\n",
			this.conn.getFloat("/position/latitude-deg"),
			this.conn.getFloat("/position/longitude-deg"));
		out.printf ("Total Distance (nmi):      %.2f\n",
			this.conn.getFloat("/autopilot/route-manager/total-distance"));
		out.printf ("Distance Remaining (nmi):  %.2f\n",
			this.conn.getFloat("/autopilot/route-manager/distance-remaining-nm"));
		out.println("Flight Time:               "
			+ this.conn.get("/autopilot/route-manager/flight-time-string"));
		out.println("Time Remaining:            "
			+ this.conn.get("/autopilot/route-manager/ete-string"));
		out.printf ("Fuel:                      %.2f%%\n",
			this.conn.getFloat("/consumables/fuel/total-fuel-norm") * 100.0);
		out.println("Paused:                    "
			+ ((this.conn.getBoolean("/sim/freeze/clock") && this.conn.getBoolean("/sim/freeze/master")) ? "True" : "False"));
		out.println("Crashed:                   "
			+ (this.conn.getBoolean("/sim/crashed") ? "True" : "False"));
	}

	public void doGet(HttpServletRequest request, HttpServletResponse response)
		throws IOException, ServletException {
		doPost(request, response);
	}
}

